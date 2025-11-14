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

namespace Marvin\Device\Infrastructure\Framework\Symfony\Service\Acl;

use Marvin\Device\Application\Service\Acl\ProtocolCapabilityServiceInterface;
use Marvin\Device\Domain\Model\PendingAction;
use Marvin\Device\Domain\Repository\PendingActionRepositoryInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\PendingActionStatus;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Protocol\Application\Command\SendDeviceCommand;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

/**
 * ACL Service: Device → Protocol (capabilities & actions)
 *
 * Allows Device Context to:
 * - Query protocol capabilities support
 * - Execute device actions via Protocol Context
 *
 * This is a TEMPORARY implementation with hardcoded capability mappings.
 * TODO: Implement proper protocol adapter capability discovery
 */
final readonly class ProtocolCapabilityService implements ProtocolCapabilityServiceInterface
{
    // TODO: Replace with dynamic capability discovery from Protocol Context adapters
    private const array PROTOCOL_CAPABILITIES = [
        'zigbee' => [
            'switch', 'brightness', 'color_control', 'color_temperature',
            'temperature_measurement', 'humidity_measurement', 'pressure_measurement',
            'motion_sensor', 'contact_sensor', 'lock', 'window_covering',
            'battery', 'power_meter', 'energy_meter',
        ],
        'mqtt' => [
            'switch', 'brightness', 'temperature_measurement', 'humidity_measurement',
            'motion_sensor', 'contact_sensor', 'power_meter',
        ],
        'network' => [
            'switch', 'brightness', 'color_control',
            'power_meter', 'energy_meter',
        ],
        'bluetooth' => [
            'temperature_measurement', 'humidity_measurement', 'battery',
            'motion_sensor', 'contact_sensor',
        ],
    ];

    private const int DEFAULT_TIMEOUT = 5000; // milliseconds
    private const int POLLING_INTERVAL = 100; // milliseconds

    public function __construct(
        private MessageBusInterface $commandBus,
        private PendingActionRepositoryInterface $pendingActionRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function getSupportedCapabilities(Protocol $protocol): array
    {
        $capabilityStrings = self::PROTOCOL_CAPABILITIES[$protocol->value] ?? [];

        // Convert strings to Capability enums
        return array_map(
            Capability::from(...),
            $capabilityStrings
        );
    }

    public function supportsCapability(Protocol $protocol, Capability $capability): bool
    {
        $supportedCapabilities = $this->getSupportedCapabilities($protocol);

        return array_any($supportedCapabilities, fn ($supportedCapability) => $supportedCapability === $capability);
    }

    public function supportsNativeGroups(Protocol $protocol): bool
    {
        // Zigbee2MQTT supports native groups, others don't (for now)
        return match ($protocol) {
            Protocol::ZIGBEE => true,
            Protocol::MATTER => true, // Future support
            default => false,
        };
    }

    public function supportsNativeScenes(Protocol $protocol): bool
    {
        // Zigbee2MQTT and Matter support native scenes
        return match ($protocol) {
            Protocol::ZIGBEE, Protocol::MATTER => true, // Future support
            default => false,
        };
    }

    public function executeAction(
        string $protocolId,
        string $nativeId,
        string $capability,
        string $action,
        array $parameters = [],
        int $timeout = 5000
    ): array {
        $deviceId = new DeviceId($protocolId); // Assuming protocolId is actually deviceId

        $this->logger->info('Executing device action via Protocol Context', [
            'deviceId' => $deviceId->toString(),
            'nativeId' => $nativeId,
            'capability' => $capability,
            'action' => $action,
            'parameters' => $parameters,
            'timeout' => $timeout,
        ]);

        // Determine execution mode based on timeout
        // timeout > 0 → Try CORRELATION_ID first, fallback to DEVICE_LOCK
        // timeout = 0 → FIRE_AND_FORGET
        if ($timeout === 0) {
            return $this->executeFireAndForget($deviceId, $action, $parameters);
        }

        // Try CORRELATION_ID mode first (preferred for performance)
        try {
            return $this->executeWithCorrelation($deviceId, $capability, $action, $parameters, $timeout);
        } catch (Throwable $e) {
            $this->logger->warning('CORRELATION_ID mode failed, trying DEVICE_LOCK', [
                'error' => $e->getMessage(),
            ]);

            // Fallback to DEVICE_LOCK mode
            return $this->executeWithDeviceLock($deviceId, $capability, $action, $parameters, $timeout);
        }
    }

    /**
     * Execute action in FIRE_AND_FORGET mode (asynchronous, no wait)
     */
    private function executeFireAndForget(DeviceId $deviceId, string $action, array $parameters): array
    {
        $command = new SendDeviceCommand(
            deviceId: $deviceId,
            action: $action,
            parameters: $parameters,
            executionMode: ExecutionMode::FIRE_AND_FORGET,
        );

        $this->commandBus->dispatch($command);

        $this->logger->info('Action sent in FIRE_AND_FORGET mode', [
            'deviceId' => $deviceId->toString(),
        ]);

        return [
            'success' => true,
            'mode' => 'fire_and_forget',
            'message' => 'Command sent, no response expected',
        ];
    }

    /**
     * Execute action with CORRELATION_ID mode (synchronous with correlation)
     */
    private function executeWithCorrelation(
        DeviceId $deviceId,
        string $capability,
        string $action,
        array $parameters,
        int $timeoutMs
    ): array {
        // Check if device has active pending action (prevents concurrent actions)
        if ($this->pendingActionRepository->hasActivePendingAction($deviceId)) {
            throw new RuntimeException('Device already has an active pending action');
        }

        // Create PendingAction with correlation ID
        $correlationId = new CorrelationId();
        $pendingAction = PendingAction::createWithCorrelation(
            deviceId: $deviceId,
            correlationId: $correlationId,
            capability: $capability,
            action: $action,
            parameters: $parameters,
            timeoutSeconds: (int) ceil($timeoutMs / 1000),
        );

        $this->pendingActionRepository->save($pendingAction);

        // Dispatch command to Protocol Context
        $command = new SendDeviceCommand(
            deviceId: $deviceId,
            action: $action,
            parameters: array_merge($parameters, [
                '_correlationId' => $correlationId->toString(),
            ]),
            executionMode: ExecutionMode::CORRELATION_ID,
        );

        $this->commandBus->dispatch($command);

        // Poll for completion
        return $this->pollPendingAction($pendingAction, $timeoutMs);
    }

    /**
     * Execute action with DEVICE_LOCK mode (synchronous with device lock)
     */
    private function executeWithDeviceLock(
        DeviceId $deviceId,
        string $capability,
        string $action,
        array $parameters,
        int $timeoutMs
    ): array {
        // Check if device is locked
        if ($this->pendingActionRepository->hasActivePendingAction($deviceId)) {
            throw new RuntimeException('Device is locked by another action');
        }

        // Create PendingAction (device lock)
        $pendingAction = PendingAction::createWithDeviceLock(
            deviceId: $deviceId,
            capability: $capability,
            action: $action,
            parameters: $parameters,
            timeoutSeconds: (int) ceil($timeoutMs / 1000),
        );

        $this->pendingActionRepository->save($pendingAction);

        // Dispatch command to Protocol Context
        $command = new SendDeviceCommand(
            deviceId: $deviceId,
            action: $action,
            parameters: $parameters,
            executionMode: ExecutionMode::DEVICE_LOCK,
        );

        $this->commandBus->dispatch($command);

        // Poll for completion
        return $this->pollPendingAction($pendingAction, $timeoutMs);
    }

    /**
     * Poll PendingAction until completion or timeout
     */
    private function pollPendingAction(PendingAction $pendingAction, int $timeoutMs): array
    {
        $startTime = microtime(true);
        $timeoutSeconds = $timeoutMs / 1000;

        while (true) {
            // Refresh from database
            $fresh = $this->pendingActionRepository->byId($pendingAction->id);

            if ($fresh === null) {
                throw new RuntimeException('PendingAction disappeared during polling');
            }

            // Check if completed
            if ($fresh->status === PendingActionStatus::COMPLETED) {
                $this->logger->info('Action completed successfully', [
                    'deviceId' => $fresh->deviceId->toString(),
                    'duration' => (microtime(true) - $startTime) * 1000 . 'ms',
                ]);

                return [
                    'success' => true,
                    'result' => $fresh->result,
                    'duration_ms' => (microtime(true) - $startTime) * 1000,
                ];
            }

            // Check if failed
            if ($fresh->status === PendingActionStatus::FAILED) {
                $this->logger->error('Action failed', [
                    'deviceId' => $fresh->deviceId->toString(),
                    'error' => $fresh->errorMessage,
                ]);

                return [
                    'success' => false,
                    'error' => $fresh->errorMessage,
                ];
            }

            // Check if timeout exceeded
            $elapsed = microtime(true) - $startTime;
            if ($elapsed >= $timeoutSeconds) {
                // Mark as timeout
                $fresh->timeout();
                $this->pendingActionRepository->save($fresh);

                $this->logger->warning('Action timed out', [
                    'deviceId' => $fresh->deviceId->toString(),
                    'timeout' => $timeoutMs . 'ms',
                ]);

                return [
                    'success' => false,
                    'error' => sprintf('Action timed out after %dms', $timeoutMs),
                ];
            }

            // Sleep before next poll
            usleep(self::POLLING_INTERVAL * 1000);
        }
    }

    public function createNativeGroup(
        Protocol $protocol,
        string $protocolId,
        string $groupFriendlyName,
        array $deviceNativeIds
    ): array {
        $this->logger->info('Creating native group', [
            'protocol' => $protocol->value,
            'protocolId' => $protocolId,
            'groupFriendlyName' => $groupFriendlyName,
            'deviceCount' => count($deviceNativeIds),
        ]);

        // Check if protocol supports native groups
        if (!$this->supportsNativeGroups($protocol)) {
            $this->logger->warning('Protocol does not support native groups', [
                'protocol' => $protocol->value,
            ]);

            return [
                'success' => false,
                'error' => sprintf('Protocol %s does not support native groups', $protocol->value),
            ];
        }

        // For Zigbee2MQTT, create native group via bridge API
        if ($protocol === Protocol::ZIGBEE) {
            return $this->createZigbeeNativeGroup($protocolId, $groupFriendlyName, $deviceNativeIds);
        }

        // For Matter (future support)
        if ($protocol === Protocol::MATTER) {
            $this->logger->warning('Matter native groups not yet implemented');

            return [
                'success' => false,
                'error' => 'Matter native groups not yet implemented',
            ];
        }

        return [
            'success' => false,
            'error' => 'Unsupported protocol for native groups',
        ];
    }

    /**
     * Create Zigbee native group via Zigbee2MQTT bridge API
     *
     * Zigbee2MQTT API:
     * Topic: zigbee2mqtt/bridge/request/group/add
     * Payload: {"friendly_name": "group_name", "id": 123}
     *
     * Then add devices to group:
     * Topic: zigbee2mqtt/bridge/request/group/members/add
     * Payload: {"group": "group_name", "device": "device_friendly_name"}
     */
    private function createZigbeeNativeGroup(
        string $protocolId,
        string $groupFriendlyName,
        array $deviceNativeIds
    ): array {
        try {
            // Generate unique group ID (Zigbee groups use 16-bit IDs: 0x0000-0xFFF7)
            // Use a random ID in the user-defined range (0x0000-0xFFF7)
            $groupId = random_int(0x0001, 0xFFF7);

            $this->logger->info('Creating Zigbee group via bridge API', [
                'groupId' => $groupId,
                'groupFriendlyName' => $groupFriendlyName,
            ]);

            // Step 1: Create the group
            // Send command to Protocol Context to create the group
            // The Protocol Context will publish to zigbee2mqtt/bridge/request/group/add
            $createCommand = new SendDeviceCommand(
                deviceId: new DeviceId($protocolId), // Use protocol as "device" for bridge commands
                action: 'bridge_group_add',
                parameters: [
                    'friendly_name' => $groupFriendlyName,
                    'id' => $groupId,
                ],
                executionMode: ExecutionMode::FIRE_AND_FORGET,
            );

            $this->commandBus->dispatch($createCommand);

            // Step 2: Add devices to the group
            foreach ($deviceNativeIds as $deviceNativeId) {
                $this->logger->debug('Adding device to Zigbee group', [
                    'device' => $deviceNativeId,
                    'group' => $groupFriendlyName,
                ]);

                $addMemberCommand = new SendDeviceCommand(
                    deviceId: new DeviceId($protocolId),
                    action: 'bridge_group_members_add',
                    parameters: [
                        'group' => $groupFriendlyName,
                        'device' => $deviceNativeId,
                    ],
                    executionMode: ExecutionMode::FIRE_AND_FORGET,
                );

                $this->commandBus->dispatch($addMemberCommand);
            }

            $this->logger->info('Zigbee native group created successfully', [
                'groupId' => $groupId,
                'groupFriendlyName' => $groupFriendlyName,
                'deviceCount' => count($deviceNativeIds),
            ]);

            return [
                'success' => true,
                'nativeGroupId' => (string) $groupId,
                'friendlyName' => $groupFriendlyName,
            ];
        } catch (Throwable $e) {
            $this->logger->error('Failed to create Zigbee native group', [
                'error' => $e->getMessage(),
                'groupFriendlyName' => $groupFriendlyName,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createNativeScene(
        Protocol $protocol,
        string $protocolId,
        string $sceneFriendlyName,
        array $sceneStates,
        ?string $groupId = null
    ): array {
        $this->logger->info('Creating native scene', [
            'protocol' => $protocol->value,
            'protocolId' => $protocolId,
            'sceneFriendlyName' => $sceneFriendlyName,
            'deviceCount' => count($sceneStates),
            'groupId' => $groupId,
        ]);

        // Check if protocol supports native scenes
        if (!$this->supportsNativeScenes($protocol)) {
            $this->logger->warning('Protocol does not support native scenes', [
                'protocol' => $protocol->value,
            ]);

            return [
                'success' => false,
                'error' => sprintf('Protocol %s does not support native scenes', $protocol->value),
            ];
        }

        // For Zigbee2MQTT, create native scene via bridge API
        if ($protocol === Protocol::ZIGBEE) {
            return $this->createZigbeeNativeScene($protocolId, $sceneFriendlyName, $sceneStates, $groupId);
        }

        // For Matter (future support)
        if ($protocol === Protocol::MATTER) {
            $this->logger->warning('Matter native scenes not yet implemented');

            return [
                'success' => false,
                'error' => 'Matter native scenes not yet implemented',
            ];
        }

        return [
            'success' => false,
            'error' => 'Unsupported protocol for native scenes',
        ];
    }

    /**
     * Create Zigbee native scene via Zigbee2MQTT bridge API
     *
     * Zigbee2MQTT API:
     * Topic: zigbee2mqtt/bridge/request/scene/add
     * Payload: {"id": sceneId, "friendly_name": "scene_name", "group": groupId}
     *
     * Then store device states:
     * Topic: zigbee2mqtt/bridge/request/scene/store
     * Payload: {"id": sceneId, "device": "device_friendly_name"}
     */
    private function createZigbeeNativeScene(
        string $protocolId,
        string $sceneFriendlyName,
        array $sceneStates,
        ?string $groupId = null
    ): array {
        try {
            // Generate unique scene ID (Zigbee scenes use 8-bit IDs: 0x00-0xFE)
            // Scene ID 0xFF is reserved
            $sceneId = random_int(0x01, 0xFE);

            $this->logger->info('Creating Zigbee scene via bridge API', [
                'sceneId' => $sceneId,
                'sceneFriendlyName' => $sceneFriendlyName,
                'groupId' => $groupId,
            ]);

            // Step 1: Create the scene
            $createParams = [
                'id' => $sceneId,
                'friendly_name' => $sceneFriendlyName,
            ];

            // If groupId provided, attach scene to group
            if ($groupId !== null) {
                $createParams['group'] = (int) $groupId;
            }

            $createCommand = new SendDeviceCommand(
                deviceId: new DeviceId($protocolId),
                action: 'bridge_scene_add',
                parameters: $createParams,
                executionMode: ExecutionMode::FIRE_AND_FORGET,
            );

            $this->commandBus->dispatch($createCommand);

            // Step 2: Store states for each device
            foreach ($sceneStates as $deviceNativeId => $states) {
                $this->logger->debug('Storing scene state for device', [
                    'scene' => $sceneFriendlyName,
                    'device' => $deviceNativeId,
                    'states' => $states,
                ]);

                // First, apply the states to the device
                foreach ($states as $capability => $value) {
                    $this->logger->debug('Setting device state before storing scene', [
                        'device' => $deviceNativeId,
                        'capability' => $capability,
                        'value' => $value,
                    ]);

                    // Apply state to device (device must be in target state before storing)
                    // This is done via normal device command
                    $setStateCommand = new SendDeviceCommand(
                        deviceId: new DeviceId($protocolId),
                        action: 'set_' . $capability,
                        parameters: array_merge(['device' => $deviceNativeId], [$capability => $value]),
                        executionMode: ExecutionMode::FIRE_AND_FORGET,
                    );

                    $this->commandBus->dispatch($setStateCommand);
                }

                // Then store current device state into the scene
                $storeCommand = new SendDeviceCommand(
                    deviceId: new DeviceId($protocolId),
                    action: 'bridge_scene_store',
                    parameters: [
                        'id' => $sceneId,
                        'device' => $deviceNativeId,
                    ],
                    executionMode: ExecutionMode::FIRE_AND_FORGET,
                );

                $this->commandBus->dispatch($storeCommand);
            }

            $this->logger->info('Zigbee native scene created successfully', [
                'sceneId' => $sceneId,
                'sceneFriendlyName' => $sceneFriendlyName,
                'deviceCount' => count($sceneStates),
            ]);

            return [
                'success' => true,
                'nativeSceneId' => (string) $sceneId,
                'friendlyName' => $sceneFriendlyName,
                'groupId' => $groupId,
            ];
        } catch (Throwable $e) {
            $this->logger->error('Failed to create Zigbee native scene', [
                'error' => $e->getMessage(),
                'sceneFriendlyName' => $sceneFriendlyName,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function updateNativeScene(
        Protocol $protocol,
        string $protocolId,
        string $nativeSceneId,
        array $sceneStates
    ): array {
        $this->logger->info('Updating native scene', [
            'protocol' => $protocol->value,
            'protocolId' => $protocolId,
            'nativeSceneId' => $nativeSceneId,
            'deviceCount' => count($sceneStates),
        ]);

        // Check if protocol supports native scenes
        if (!$this->supportsNativeScenes($protocol)) {
            $this->logger->warning('Protocol does not support native scenes', [
                'protocol' => $protocol->value,
            ]);

            return [
                'success' => false,
                'error' => sprintf('Protocol %s does not support native scenes', $protocol->value),
            ];
        }

        // For Zigbee2MQTT, update native scene via bridge API
        if ($protocol === Protocol::ZIGBEE) {
            return $this->updateZigbeeNativeScene($protocolId, $nativeSceneId, $sceneStates);
        }

        // For Matter (future support)
        if ($protocol === Protocol::MATTER) {
            $this->logger->warning('Matter native scenes not yet implemented');

            return [
                'success' => false,
                'error' => 'Matter native scenes not yet implemented',
            ];
        }

        return [
            'success' => false,
            'error' => 'Unsupported protocol for native scenes',
        ];
    }

    /**
     * Update Zigbee native scene via Zigbee2MQTT bridge API
     *
     * Updates an existing scene by applying new states to devices and storing them
     *
     * Zigbee2MQTT API:
     * Topic: zigbee2mqtt/bridge/request/scene/store
     * Payload: {"id": sceneId, "device": "device_friendly_name"}
     */
    private function updateZigbeeNativeScene(
        string $protocolId,
        string $nativeSceneId,
        array $sceneStates
    ): array {
        try {
            $sceneId = (int) $nativeSceneId;

            $this->logger->info('Updating Zigbee scene via bridge API', [
                'sceneId' => $sceneId,
            ]);

            // Store states for each device
            foreach ($sceneStates as $deviceNativeId => $states) {
                $this->logger->debug('Updating scene state for device', [
                    'sceneId' => $sceneId,
                    'device' => $deviceNativeId,
                    'states' => $states,
                ]);

                // First, apply the states to the device
                foreach ($states as $capability => $value) {
                    $this->logger->debug('Setting device state before storing scene', [
                        'device' => $deviceNativeId,
                        'capability' => $capability,
                        'value' => $value,
                    ]);

                    // Apply state to device (device must be in target state before storing)
                    $setStateCommand = new SendDeviceCommand(
                        deviceId: new DeviceId($protocolId),
                        action: 'set_' . $capability,
                        parameters: array_merge(['device' => $deviceNativeId], [$capability => $value]),
                        executionMode: ExecutionMode::FIRE_AND_FORGET,
                    );

                    $this->commandBus->dispatch($setStateCommand);
                }

                // Then store current device state into the scene
                $storeCommand = new SendDeviceCommand(
                    deviceId: new DeviceId($protocolId),
                    action: 'bridge_scene_store',
                    parameters: [
                        'id' => $sceneId,
                        'device' => $deviceNativeId,
                    ],
                    executionMode: ExecutionMode::FIRE_AND_FORGET,
                );

                $this->commandBus->dispatch($storeCommand);
            }

            $this->logger->info('Zigbee native scene updated successfully', [
                'sceneId' => $sceneId,
                'deviceCount' => count($sceneStates),
            ]);

            return [
                'success' => true,
            ];
        } catch (Throwable $e) {
            $this->logger->error('Failed to update Zigbee native scene', [
                'error' => $e->getMessage(),
                'nativeSceneId' => $nativeSceneId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
