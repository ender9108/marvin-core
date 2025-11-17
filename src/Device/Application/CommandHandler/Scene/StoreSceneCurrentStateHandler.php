<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */
declare(strict_types=1);

namespace Marvin\Device\Application\CommandHandler\Scene;

use InvalidArgumentException;
use Marvin\Device\Application\Command\Scene\StoreSceneCurrentState;
use Marvin\Device\Application\Service\Acl\ProtocolCapabilityServiceInterface;
use Marvin\Device\Domain\Exception\DeviceNotFound;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\SceneStates;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for StoreSceneCurrentState command
 *
 * Captures the current state of all devices in a scene
 * This is typically used to "snapshot" a desired configuration
 */
#[AsMessageHandler]
final readonly class StoreSceneCurrentStateHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private ProtocolCapabilityServiceInterface $protocolCapability,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(StoreSceneCurrentState $command): void
    {
        $this->logger->info('Storing scene current state', [
            'sceneId' => $command->sceneId->toString(),
        ]);

        $scene = $this->deviceRepository->byId($command->sceneId);

        if (!$scene->isComposite()) {
            throw new InvalidArgumentException(sprintf(
                'Device %s is not a composite device (scene)',
                $command->sceneId->toString()
            ));
        }

        // Determine which devices to capture
        $deviceIds = $command->deviceIds ?? $scene->childDeviceIds;

        // Load all devices
        $devices = [];
        foreach ($deviceIds as $deviceId) {
            try {
                $device = $this->deviceRepository->byId($deviceId);
                $devices[] = $device;
            } catch (DeviceNotFound) {
                $this->logger->warning('Device not found while storing scene state', [
                    'deviceId' => $deviceId->toString(),
                ]);
            }
        }

        // Capture current state of all devices
        $states = $this->captureDeviceStates($devices);

        // Update scene states in the aggregate
        $sceneStates = SceneStates::fromArray($states);
        $scene->updateSceneStates($sceneStates);
        $this->deviceRepository->save($scene);

        $this->logger->info('Scene current state stored', [
            'sceneId' => $scene->id->toString(),
            'deviceCount' => count($states),
        ]);

        // Update native scene if it exists
        if ($scene->nativeSceneInfo !== null) {
            $this->updateNativeScene($scene, $devices, $sceneStates);
        }
    }

    /**
     * Capture current states of all devices
     *
     * @param Device[] $devices
     * @return array<string, array<string, mixed>> Device states indexed by device ID
     */
    private function captureDeviceStates(array $devices): array
    {
        $states = [];

        foreach ($devices as $device) {
            $currentState = $device->getCurrentState();

            if (!empty($currentState)) {
                $states[$device->id->toString()] = $currentState;
            }
        }

        return $states;
    }

    /**
     * Update the native scene in the protocol
     *
     * Uses the Zigbee bridge "scene/store" command to update the scene with current device states
     *
     * @param Device[] $devices
     */
    private function updateNativeScene(Device $scene, array $devices, SceneStates $sceneStates): void
    {
        $nativeSceneInfo = $scene->nativeSceneInfo;

        if ($nativeSceneInfo === null || $scene->protocol === null || $scene->protocolId === null) {
            $this->logger->warning('Cannot update native scene: missing protocol information');
            return;
        }

        $this->logger->info('Updating native scene', [
            'sceneId' => $scene->id->toString(),
            'nativeSceneId' => $nativeSceneInfo->nativeSceneId,
            'protocol' => $scene->protocol->value,
        ]);

        // Build scene states array [deviceNativeId => [capability => value]]
        $nativeSceneStates = [];

        foreach ($sceneStates->toArray() as $deviceIdString => $states) {
            // Find device to get its nativeId
            $device = array_find(
                $devices,
                fn (Device $d) => $d->id->toString() === $deviceIdString
            );

            if ($device !== null) {
                $nativeId = $device->physicalAddress !== null
                    ? $device->physicalAddress->value
                    : $device->id->toString();
                $nativeSceneStates[$nativeId] = $states;
            }
        }

        // Update the native scene with new states
        $result = $this->protocolCapability->updateNativeScene(
            protocol: $scene->protocol,
            protocolId: $scene->protocolId->toString(),
            nativeSceneId: $nativeSceneInfo->nativeSceneId,
            sceneStates: $nativeSceneStates
        );

        if (!$result['success']) {
            $this->logger->error('Failed to update native scene', [
                'sceneId' => $scene->id->toString(),
                'nativeSceneId' => $nativeSceneInfo->nativeSceneId,
                'error' => $result['error'] ?? 'Unknown error',
            ]);
            return;
        }

        $this->logger->info('Native scene updated successfully', [
            'sceneId' => $scene->id->toString(),
            'nativeSceneId' => $nativeSceneInfo->nativeSceneId,
        ]);
    }
}
