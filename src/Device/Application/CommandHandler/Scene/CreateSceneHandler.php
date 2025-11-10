<?php

declare(strict_types=1);

namespace Marvin\Device\Application\CommandHandler\Scene;

use DateTimeImmutable;
use Marvin\Device\Application\Command\Scene\CreateScene;
use Marvin\Device\Application\Service\Acl\ProtocolCapabilityServiceInterface;
use Marvin\Device\Domain\Exception\DeviceNotFound;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Marvin\Device\Domain\ValueObject\NativeSceneInfo;
use Marvin\Device\Domain\ValueObject\SceneStates;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for CreateScene command
 *
 * Creates a composite device (SCENE) that stores and restores predefined states
 * Scenes can be created empty and populated later with StoreSceneCurrentState
 */
#[AsMessageHandler]
final readonly class CreateSceneHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private ProtocolCapabilityServiceInterface $protocolCapability,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(CreateScene $command): Device
    {
        $this->logger->info('Creating scene', [
            'label' => $command->label->value,
            'hasStates' => $command->sceneStates !== null,
        ]);

        // Extract child device IDs from scene states (if provided)
        $childDeviceIds = [];
        $sceneStates = $command->sceneStates ?? SceneStates::empty();
        $childDevices = [];

        if ($command->sceneStates !== null) {
            $childDeviceIds = array_map(
                DeviceId::fromString(...),
                $command->sceneStates->getDeviceIds()
            );

            // Load child devices for native scene creation
            foreach ($childDeviceIds as $deviceId) {
                try {
                    $device = $this->deviceRepository->byId($deviceId);
                    $childDevices[] = $device;
                } catch (DeviceNotFound) {
                    $this->logger->warning('Failed to load children device for scene', [
                        'deviceId' => $deviceId->toString(),
                    ]);
                }
            }
        }

        // Find common native group for scene attachment (if any)
        $nativeGroupId = $this->findCommonNativeGroup($childDevices);

        // Try to create native scene if strategy allows
        $nativeSceneInfo = $this->tryCreateNativeScene(
            $childDevices,
            $command->compositeStrategy,
            $command->label->value,
            $sceneStates,
            $nativeGroupId
        );

        // Create scene device
        $scene = Device::createComposite(
            label: $command->label,
            compositeType: CompositeType::SCENE,
            childDeviceIds: $childDeviceIds,
            capabilities: [], // Scenes don't expose capabilities directly
            compositeStrategy: $command->compositeStrategy,
            executionStrategy: $command->executionStrategy,
            nativeSceneInfo: $nativeSceneInfo,
            sceneStates: $sceneStates,
            zoneId: $command->zoneId,
            description: $command->description,
            metadata: $command->metadata,
        );

        // Save scene
        $this->deviceRepository->save($scene);

        $this->logger->info('Scene created successfully', [
            'sceneId' => $scene->id->toString(),
            'label' => $scene->label->value,
            'deviceCount' => count($childDeviceIds),
            'hasNativeScene' => null !== $scene->nativeSceneInfo,
        ]);

        return $scene;
    }

    /**
     * Try to create a native protocol scene if possible
     *
     * Behavior depends on CompositeStrategy:
     * - EMULATED_ONLY: Skip native creation, return null
     * - NATIVE_IF_AVAILABLE: Try native, fallback to null if fails (default)
     * - NATIVE_ONLY: Try native, throw exception if fails
     *
     * @param Device[] $childDevices
     * @param string|null $nativeGroupId Optional native group ID to attach the scene to
     */
    private function tryCreateNativeScene(
        array $childrenDevices,
        CompositeStrategy $strategy,
        string $sceneLabel,
        SceneStates $sceneStates,
        ?string $nativeGroupId = null
    ): ?NativeSceneInfo {
        // EMULATED_ONLY: Don't even try native
        if ($strategy === CompositeStrategy::EMULATED_ONLY) {
            $this->logger->debug('Strategy is EMULATED_ONLY, skipping native scene creation');
            return null;
        }

        // Need at least one device to create native scene
        if (empty($childrenDevices)) {
            $this->logger->debug('No devices in scene, cannot create native scene');
            return null;
        }

        // Check if all devices use the same protocol
        $protocols = array_unique(array_map(
            fn (Device $device) => $device->protocol?->value,
            $childrenDevices
        ));

        if (count($protocols) !== 1) {
            $this->logger->debug('Cannot create native scene: devices use different protocols', [
                'protocols' => $protocols,
                'strategy' => $strategy->value,
            ]);

            // NATIVE_ONLY: Must have same protocol
            if ($strategy === CompositeStrategy::NATIVE_ONLY) {
                throw new RuntimeException(
                    'Cannot create native scene: devices use different protocols. Use EMULATED_ONLY strategy for mixed protocols.'
                );
            }

            // NATIVE_IF_AVAILABLE: Fallback to emulation
            return null;
        }

        $protocol = $childrenDevices[0]->protocol;
        $protocolId = $childrenDevices[0]->protocolId;

        if ($protocol === null || $protocolId === null) {
            if ($strategy === CompositeStrategy::NATIVE_ONLY) {
                throw new RuntimeException('Cannot create native scene: devices have no protocol');
            }
            return null;
        }

        // Check if protocol supports native scenes
        if (!$this->protocolCapability->supportsNativeScenes($protocol)) {
            $this->logger->debug('Protocol does not support native scenes', [
                'protocol' => $protocol->value,
                'strategy' => $strategy->value,
            ]);

            // NATIVE_ONLY: Protocol must support native scenes
            if ($strategy === CompositeStrategy::NATIVE_ONLY) {
                throw new RuntimeException(sprintf(
                    'Protocol %s does not support native scenes. Use EMULATED_ONLY strategy instead.',
                    $protocol->value
                ));
            }

            // NATIVE_IF_AVAILABLE: Fallback to emulation
            return null;
        }

        // Build scene states array [deviceNativeId => [capability => value]]
        $nativeSceneStates = [];
        foreach ($sceneStates->toArray() as $deviceIdString => $states) {
            // Find device to get its nativeId
            $device = array_find(
                $childrenDevices,
                fn (Device $d) => $d->id->toString() === $deviceIdString
            );

            if ($device !== null) {
                $nativeId = $device->physicalAddress !== null
                    ? $device->physicalAddress->value
                    : $device->id->toString();
                $nativeSceneStates[$nativeId] = $states;
            }
        }

        // Generate friendly name for native scene (sanitized)
        $sceneFriendlyName = $this->sanitizeFriendlyName($sceneLabel);

        // Create native scene via Protocol Context
        // Attach to native group if available (improves performance)
        $result = $this->protocolCapability->createNativeScene(
            protocol: $protocol,
            protocolId: $protocolId->toString(),
            sceneFriendlyName: $sceneFriendlyName,
            sceneStates: $nativeSceneStates,
            groupId: $nativeGroupId
        );

        if (!$result['success']) {
            $this->logger->warning('Failed to create native scene', [
                'protocol' => $protocol->value,
                'strategy' => $strategy->value,
                'error' => $result['error'] ?? 'Unknown error',
            ]);

            // NATIVE_ONLY: Propagate error
            if ($strategy === CompositeStrategy::NATIVE_ONLY) {
                throw new RuntimeException(sprintf(
                    'Failed to create native scene: %s',
                    $result['error'] ?? 'Unknown error'
                ));
            }

            // NATIVE_IF_AVAILABLE: Fallback to emulated
            return null;
        }

        $nativeSceneId = $result['nativeSceneId'] ?? '';
        $friendlyName = $result['friendlyName'] ?? $sceneFriendlyName;
        $resultGroupId = $result['groupId'] ?? null;

        $this->logger->info('Native scene created successfully', [
            'protocol' => $protocol->value,
            'nativeSceneId' => $nativeSceneId,
            'friendlyName' => $friendlyName,
            'attachedToGroup' => $nativeGroupId !== null,
            'nativeGroupId' => $nativeGroupId,
        ]);

        return NativeSceneInfo::create(
            nativeSceneId: $nativeSceneId,
            protocolId: $protocolId->toString(),
            friendlyName: $friendlyName,
            groupId: $resultGroupId,
            metadata: [
                'protocol' => $protocol->value,
                'createdAt' => new DateTimeImmutable()->format('c'),
            ]
        );
    }

    /**
     * Find a native group that contains all scene devices
     *
     * If all devices in the scene belong to the same native group,
     * we can attach the scene to that group for better performance
     *
     * @param Device[] $childDevices
     * @return string|null The native group ID, or null if no common native group
     */
    private function findCommonNativeGroup(array $childrenDevices): ?string
    {
        if (empty($childrenDevices)) {
            return null;
        }

        // Find groups for first device
        $firstDeviceGroups = $this->deviceRepository->byChildDeviceId($childrenDevices[0]->id);

        // Filter to only groups with native group info
        $nativeGroups = array_filter(
            $firstDeviceGroups,
            fn (Device $group) => $group->compositeType === CompositeType::GROUP
                && $group->nativeGroupInfo !== null
        );

        if (empty($nativeGroups)) {
            return null;
        }

        // Check each native group to see if it contains all scene devices
        foreach ($nativeGroups as $group) {
            $groupDeviceIds = array_map(
                fn ($deviceId) => $deviceId->toString(),
                $group->childDeviceIds
            );

            $sceneDeviceIds = array_map(
                fn (Device $device) => $device->id->toString(),
                $childrenDevices
            );

            // Check if all scene devices are in this group
            $allDevicesInGroup = empty(array_diff($sceneDeviceIds, $groupDeviceIds));

            if ($allDevicesInGroup && $group->nativeGroupInfo !== null) {
                $nativeGroupId = $group->nativeGroupInfo->nativeGroupId;

                $this->logger->info('Found native group containing all scene devices', [
                    'nativeGroupId' => $nativeGroupId,
                    'groupLabel' => $group->label->value,
                    'deviceCount' => count($childrenDevices),
                ]);

                return $nativeGroupId;
            }
        }

        $this->logger->debug('No native group found containing all scene devices', [
            'deviceCount' => count($childrenDevices),
        ]);

        return null;
    }

    /**
     * Sanitize scene friendly name for protocol compatibility
     * Removes special characters and spaces
     */
    private function sanitizeFriendlyName(string $name): string
    {
        // Replace spaces with underscores
        $sanitized = str_replace(' ', '_', $name);

        // Remove special characters (keep alphanumeric and underscores)
        $sanitized = preg_replace('/[^a-zA-Z0-9_]/', '', $sanitized);

        // Lowercase
        return strtolower($sanitized ?? '');
    }
}
