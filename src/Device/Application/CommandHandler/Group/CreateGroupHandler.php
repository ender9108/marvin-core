<?php

declare(strict_types=1);

namespace Marvin\Device\Application\CommandHandler\Group;

use DateTimeImmutable;
use Marvin\Device\Application\Command\Group\CreateGroup;
use Marvin\Device\Application\Service\Acl\ProtocolCapabilityServiceInterface;
use Marvin\Device\Domain\Exception\CannotAddCompositeDeviceOnGroup;
use Marvin\Device\Domain\Exception\FailedCreateNativeGroup;
use Marvin\Device\Domain\Exception\StrategyNotAuthorized;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Marvin\Device\Domain\ValueObject\NativeGroupInfo;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for CreateGroup command
 *
 * Creates a composite device (GROUP) that can control multiple devices together
 * Attempts to create native protocol groups when possible for better performance
 */
#[AsMessageHandler]
final readonly class CreateGroupHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private ProtocolCapabilityServiceInterface $protocolCapability,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(CreateGroup $command): Device
    {
        $this->logger->info('Creating group', [
            'label' => $command->label->value,
            'childCount' => count($command->childrenDeviceIds),
            'strategy' => $command->compositeStrategy->value,
        ]);

        // Load and validate all child devices
        $childrenDevices = $this->loadAndValidateChildDevices($command->childrenDeviceIds);

        // Determine capabilities (union of all child capabilities if not specified)
        $capabilities = empty($command->capabilities)
            ? $this->determineGroupCapabilities($childrenDevices)
            : $command->capabilities;

        // Try to create native group if strategy allows
        $nativeGroupInfo = $this->tryCreateNativeGroup(
            $childrenDevices,
            $command->compositeStrategy,
            $command->label->value
        );

        // Create group device
        $group = Device::createComposite(
            label: $command->label,
            compositeType: CompositeType::GROUP,
            childDeviceIds: $command->childrenDeviceIds,
            capabilities: $capabilities,
            compositeStrategy: $command->compositeStrategy,
            executionStrategy: $command->executionStrategy,
            nativeGroupInfo: $nativeGroupInfo,
            zoneId: $command->zoneId,
            description: $command->description,
            metadata: $command->metadata,
        );

        // Save group
        $this->deviceRepository->save($group);

        $this->logger->info('Group created successfully', [
            'groupId' => $group->id->toString(),
            'label' => $group->label->value,
            'hasNativeGroup' => null !== $group->nativeGroupInfo,
            'capabilitiesCount' => count($group->capabilities),
        ]);

        return $group;
    }

    /**
     * Load and validate all child devices exist
     *
     * @param DeviceId[] $deviceIds
     * @return Device[]
     */
    private function loadAndValidateChildDevices(array $deviceIds): array
    {
        $devices = [];

        foreach ($deviceIds as $deviceId) {
            $device = $this->deviceRepository->byId($deviceId);

            // Prevent composite devices in groups (no nested groups)
            if ($device->isComposite()) {
                throw CannotAddCompositeDeviceOnGroup::withDeviceId($deviceId);
            }

            $devices[] = $device;
        }

        return $devices;
    }

    /**
     * Determine group capabilities from child devices
     *
     * @param Device[] $childrenDevices
     * @return Capability[]
     */
    private function determineGroupCapabilities(array $childrenDevices): array
    {
        $allCapabilities = [];

        foreach ($childrenDevices as $device) {
            foreach ($device->capabilities as $deviceCapability) {
                $capability = $deviceCapability->capability;

                // Only include write capabilities (not read-only)
                if (!$capability->isReadOnly()) {
                    $allCapabilities[$capability->value] = $capability;
                }
            }
        }

        return array_values($allCapabilities);
    }

    /**
     * Try to create a native protocol group if possible
     *
     * Behavior depends on CompositeStrategy:
     * - EMULATED_ONLY: Skip native creation, return null
     * - NATIVE_IF_AVAILABLE: Try native, fallback to null if fails (default)
     * - NATIVE_ONLY: Try native, throw exception if fails
     *
     * @param Device[] $childDevices
     */
    private function tryCreateNativeGroup(
        array $childDevices,
        CompositeStrategy $strategy,
        string $groupLabel
    ): ?NativeGroupInfo {
        // EMULATED_ONLY: Don't even try native
        if ($strategy === CompositeStrategy::EMULATED_ONLY) {
            $this->logger->debug('Strategy is EMULATED_ONLY, skipping native group creation');
            return null;
        }

        // Check if all devices use the same protocol
        $protocols = array_unique(array_map(
            fn (Device $device) => $device->protocol?->value,
            $childDevices
        ));

        if (count($protocols) !== 1) {
            $this->logger->debug('Cannot create native group: devices use different protocols', [
                'protocols' => $protocols,
                'strategy' => $strategy->value,
            ]);

            // NATIVE_ONLY: Must have same protocol
            if ($strategy === CompositeStrategy::NATIVE_ONLY) {
                throw StrategyNotAuthorized::nativeOnlyWithDifferentProtocols();
            }

            // NATIVE_IF_AVAILABLE: Fallback to emulation
            return null;
        }

        $protocol = $childDevices[0]->protocol;
        $protocolId = $childDevices[0]->protocolId;

        if ($protocol === null || $protocolId === null) {
            if ($strategy === CompositeStrategy::NATIVE_ONLY) {
                throw StrategyNotAuthorized::nativeGroupWithoutProtocol();
            }
            return null;
        }

        // Check if protocol supports native groups
        if (!$this->protocolCapability->supportsNativeGroups($protocol)) {
            $this->logger->debug('Protocol does not support native groups', [
                'protocol' => $protocol->value,
                'strategy' => $strategy->value,
            ]);

            // NATIVE_ONLY: Protocol must support native groups
            if ($strategy === CompositeStrategy::NATIVE_ONLY) {
                throw StrategyNotAuthorized::protocolDoesNotSupportNativeGroup($protocol);
            }

            // NATIVE_IF_AVAILABLE: Fallback to emulation
            return null;
        }

        // Get native IDs (physicalAddress) of all child devices
        $deviceNativeIds = array_map(
            fn (Device $device) => $device->physicalAddress?->value ?? $device->id->toString(),
            $childDevices
        );

        // Generate friendly name for native group (sanitized)
        $groupFriendlyName = $this->sanitizeFriendlyName($groupLabel);

        // Create native group via Protocol Context
        $result = $this->protocolCapability->createNativeGroup(
            protocol: $protocol,
            protocolId: $protocolId->toString(),
            groupFriendlyName: $groupFriendlyName,
            deviceNativeIds: $deviceNativeIds
        );

        if (!$result['success']) {
            $this->logger->warning('Failed to create native group', [
                'protocol' => $protocol->value,
                'strategy' => $strategy->value,
                'error' => $result['error'] ?? 'Unknown error',
            ]);

            // NATIVE_ONLY: Propagate error
            if ($strategy === CompositeStrategy::NATIVE_ONLY) {
                throw new FailedCreateNativeGroup(
                    sprintf(
                        'Failed to create native group: %s',
                        $result['error'] ?? 'Unknown error'
                    ),
                    $result['error'] ?? 'Unknown error'
                );
            }

            // NATIVE_IF_AVAILABLE: Fallback to emulated
            return null;
        }

        $this->logger->info('Native group created successfully', [
            'protocol' => $protocol->value,
            'nativeGroupId' => $result['nativeGroupId'],
            'friendlyName' => $result['friendlyName'] ?? $groupFriendlyName,
        ]);

        return NativeGroupInfo::create(
            nativeGroupId: $result['nativeGroupId'],
            protocolId: $protocolId->toString(),
            friendlyName: $result['friendlyName'] ?? $groupFriendlyName,
            metadata: [
                'protocol' => $protocol->value,
                'createdAt' => new DateTimeImmutable()->format('c'),
            ]
        );
    }

    /**
     * Sanitize group friendly name for protocol compatibility
     * Removes special characters and spaces
     */
    private function sanitizeFriendlyName(string $name): string
    {
        // Replace spaces with underscores
        $sanitized = str_replace(' ', '_', $name);

        // Remove special characters (keep alphanumeric and underscores)
        $sanitized = preg_replace('/[^a-zA-Z0-9_]/', '', $sanitized);

        // Lowercase
        return strtolower((string) $sanitized);
    }
}
