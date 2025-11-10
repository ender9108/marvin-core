<?php

declare(strict_types=1);

namespace Marvin\Device\Application\CommandHandler\Scene;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use InvalidArgumentException;
use Marvin\Device\Application\Command\Device\ExecuteDeviceAction;
use Marvin\Device\Application\Command\Scene\ActivateScene;
use Marvin\Device\Application\Service\Acl\ProtocolCapabilityServiceInterface;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CapabilityAction;
use Marvin\Device\Domain\ValueObject\ExecutionStrategy;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

/**
 * Handler for ActivateScene command
 *
 * Restores all devices in a scene to their stored states
 * Dispatches ExecuteDeviceAction commands for each device
 */
#[AsMessageHandler]
final readonly class ActivateSceneHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private SyncCommandBusInterface $commandBus,
        private ProtocolCapabilityServiceInterface $protocolCapability,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(ActivateScene $command): void
    {
        $this->logger->info('Activating scene', [
            'sceneId' => $command->sceneId->toString(),
        ]);

        $scene = $this->deviceRepository->byId($command->sceneId);

        if (!$scene->isComposite()) {
            throw new InvalidArgumentException(sprintf(
                'Device %s is not a composite device (scene)',
                $command->sceneId->toString()
            ));
        }

        // Check if we have a native scene - if so, use it directly
        if ($scene->nativeSceneInfo !== null && $scene->protocol !== null && $scene->protocolId !== null) {
            $this->activateNativeScene($scene);
            return;
        }

        // Otherwise, activate scene by restoring device states
        if ($scene->sceneStates === null || empty($scene->sceneStates->toArray())) {
            $this->logger->warning('Scene has no stored states to activate', [
                'sceneId' => $command->sceneId->toString(),
            ]);
            return;
        }

        $this->activateEmulatedScene($scene);
    }

    /**
     * Activate a native scene via protocol bridge command
     *
     * Uses bridge_scene_recall for Zigbee native scenes
     */
    private function activateNativeScene(Device $scene): void
    {
        $nativeSceneInfo = $scene->nativeSceneInfo;

        if ($nativeSceneInfo === null || $scene->protocol === null || $scene->protocolId === null) {
            $this->logger->warning('Cannot activate native scene: missing protocol information');
            return;
        }

        $this->logger->info('Activating native scene', [
            'sceneId' => $scene->id->toString(),
            'nativeSceneId' => $nativeSceneInfo->nativeSceneId,
            'protocol' => $scene->protocol->value,
        ]);

        // For Zigbee: use bridge_scene_recall command
        // The ProtocolCapabilityService doesn't have a recallNativeScene method yet,
        // so we'll use executeAction directly
        $nativeId = $scene->physicalAddress !== null
            ? $scene->physicalAddress->value
            : $scene->id->toString();

        $result = $this->protocolCapability->executeAction(
            protocolId: $scene->protocolId->toString(),
            nativeId: $nativeId,
            capability: 'scene_control',
            action: 'bridge_scene_recall',
            parameters: [
                'id' => (int) $nativeSceneInfo->nativeSceneId,
            ],
        );

        if (!$result['success']) {
            $this->logger->error('Failed to activate native scene', [
                'sceneId' => $scene->id->toString(),
                'nativeSceneId' => $nativeSceneInfo->nativeSceneId,
                'error' => $result['error'] ?? 'Unknown error',
            ]);
            return;
        }

        $this->logger->info('Native scene activated successfully', [
            'sceneId' => $scene->id->toString(),
            'nativeSceneId' => $nativeSceneInfo->nativeSceneId,
        ]);
    }

    /**
     * Activate an emulated scene by restoring device states
     *
     * Dispatches ExecuteDeviceAction commands for each device
     * Respects ExecutionStrategy (SEQUENTIAL vs BROADCAST)
     */
    private function activateEmulatedScene(Device $scene): void
    {
        $executionStrategyValue = $scene->executionStrategy !== null
            ? $scene->executionStrategy->value
            : 'BROADCAST';

        $this->logger->info('Activating emulated scene', [
            'sceneId' => $scene->id->toString(),
            'executionStrategy' => $executionStrategyValue,
        ]);

        $restoredCount = 0;
        $failedCount = 0;

        if ($scene->sceneStates === null) {
            $this->logger->warning('Scene has no states to activate');
            return;
        }

        foreach ($scene->sceneStates->toArray() as $deviceIdString => $deviceState) {
            $deviceId = DeviceId::fromString($deviceIdString);

            try {
                // Map device state to actions
                $actions = $this->mapStateToActions($deviceState);

                // Execute actions for this device
                foreach ($actions as $actionData) {
                    $this->logger->debug('Executing device action from scene', [
                        'deviceId' => $deviceIdString,
                        'capability' => $actionData['capability']->value,
                        'action' => $actionData['action']->value,
                        'parameters' => $actionData['parameters'],
                    ]);

                    $command = new ExecuteDeviceAction(
                        deviceId: $deviceId,
                        capability: $actionData['capability'],
                        action: $actionData['action'],
                        parameters: $actionData['parameters']
                    );

                    $this->commandBus->handle($command);
                }

                $restoredCount++;
            } catch (Throwable $e) {
                $failedCount++;
                $this->logger->error('Failed to restore device state in scene', [
                    'sceneId' => $scene->id->toString(),
                    'deviceId' => $deviceIdString,
                    'error' => $e->getMessage(),
                ]);
            }

            // If SEQUENTIAL strategy, wait a bit between devices
            if ($scene->executionStrategy === ExecutionStrategy::SEQUENTIAL) {
                usleep(100000); // 100ms delay between devices
            }
        }

        $this->logger->info('Emulated scene activated', [
            'sceneId' => $scene->id->toString(),
            'label' => $scene->label->value,
            'restoredCount' => $restoredCount,
            'failedCount' => $failedCount,
            'totalDevices' => count($scene->sceneStates->toArray()),
        ]);
    }

    /**
     * Map device state to executable actions
     *
     * Converts capability states (e.g., "state" => "ON", "brightness" => 128)
     * to executable actions (e.g., TURN_ON, SET_BRIGHTNESS)
     *
     * @param array<string, mixed> $deviceState Device state from scene
     * @return array<int, array{capability: Capability, action: CapabilityAction, parameters: array<string, mixed>}>
     */
    private function mapStateToActions(array $deviceState): array
    {
        $actions = [];

        foreach ($deviceState as $capabilityName => $value) {
            try {
                $capability = Capability::from($capabilityName);
                $actionData = $this->determineAction($capability, $value);

                if ($actionData !== null) {
                    $actions[] = $actionData;
                }
            } catch (Throwable $e) {
                $this->logger->warning('Failed to map capability state to action', [
                    'capability' => $capabilityName,
                    'value' => $value,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $actions;
    }

    /**
     * Determine the appropriate action for a capability state
     *
     * Uses CapabilityAction::getActionsForCapability() to intelligently select
     * the best action based on the value type and available actions
     *
     * @param mixed $value State value
     * @return array{capability: Capability, action: CapabilityAction, parameters: array<string, mixed>}|null
     */
    private function determineAction(Capability $capability, mixed $value): ?array
    {
        // Get all available actions for this capability
        $availableActions = CapabilityAction::getActionsForCapability($capability);

        if (empty($availableActions)) {
            $this->logger->debug('No actions available for capability', [
                'capability' => $capability->value,
            ]);
            return null;
        }

        // Handle boolean/string state values
        if (is_string($value) || is_bool($value)) {
            $normalizedValue = is_string($value) ? strtoupper($value) : ($value ? 'ON' : 'OFF');

            // Handle ON/OFF states - look for TURN_ON/TURN_OFF in available actions
            if ($normalizedValue === 'ON' || $normalizedValue === 'TRUE' || $normalizedValue === '1') {
                if (in_array(CapabilityAction::TURN_ON, $availableActions, true)) {
                    return [
                        'capability' => $capability,
                        'action' => CapabilityAction::TURN_ON,
                        'parameters' => [],
                    ];
                }
            }

            if ($normalizedValue === 'OFF' || $normalizedValue === 'FALSE' || $normalizedValue === '0') {
                if (in_array(CapabilityAction::TURN_OFF, $availableActions, true)) {
                    return [
                        'capability' => $capability,
                        'action' => CapabilityAction::TURN_OFF,
                        'parameters' => [],
                    ];
                }
            }

            // Handle string mode values (e.g., "heat", "cool", "auto")
            // Find a SET_ action in available actions
            if (is_string($value)) {
                $setAction = $this->findSetActionInList($availableActions);
                if ($setAction !== null) {
                    return [
                        'capability' => $capability,
                        'action' => $setAction,
                        'parameters' => [$capability->value => strtolower($value)],
                    ];
                }
            }
        }

        // Handle numeric values - find SET_ action in available actions
        if (is_numeric($value)) {
            $setAction = $this->findSetActionInList($availableActions);

            if ($setAction !== null) {
                return [
                    'capability' => $capability,
                    'action' => $setAction,
                    'parameters' => [$capability->value => $value],
                ];
            }
        }

        // Handle array values (e.g., RGB color)
        if (is_array($value)) {
            $setAction = $this->findSetActionInList($availableActions);

            if ($setAction !== null) {
                return [
                    'capability' => $capability,
                    'action' => $setAction,
                    'parameters' => $value,
                ];
            }
        }

        // If we can't determine an action, log and skip
        $this->logger->debug('Could not determine action for capability state', [
            'capability' => $capability->value,
            'value' => $value,
            'type' => gettype($value),
            'availableActions' => array_map(fn ($a) => $a->value, $availableActions),
        ]);

        return null;
    }

    /**
     * @param CapabilityAction[] $actions
     */
    private function findSetActionInList(array $actions): ?CapabilityAction
    {
        $setPriority = [
            'SET_BRIGHTNESS',
            'SET_COLOR',
            'SET_COLOR_RGB',
            'SET_COLOR_HSV',
            'SET_COLOR_TEMPERATURE',
            'SET_VOLUME',
            'SET_POSITION',
            'SET_TILT',
            'SET_HEATING_SETPOINT',
            'SET_COOLING_SETPOINT',
            'SET_FAN_SPEED',
            'SET_CHANNEL',
            'SET_HUMIDITY_TARGET',
            'SET_VALVE_POSITION',
            'SET_VACUUM_FAN_SPEED',
            'SET_WATERING_DURATION',
            'SET_THERMOSTAT_MODE',
            'SET_FAN_MODE',
            'SET_PURIFIER_MODE',
            'SET_INPUT_SOURCE',
            'SET_EFFECT',
            'SET_OSCILLATION',
            'SET_REPEAT_MODE',
            'SET_SHUFFLE',
            'SET_COLOR_TEMPERATURE_MIRED',
            'SET_WARM_WHITE',
            'SET_COOL_WHITE',
            'SET_NEUTRAL_WHITE',
            'SET_COLOR_XY',
            'SET_COLOR_HEX',
            'SET_HUE',
            'SET_SATURATION',
            'SET_MODE_OFF',
            'SET_MODE_HEAT',
            'SET_MODE_COOL',
            'SET_MODE_AUTO',
            'SET_FAN_AUTO',
            'SET_FAN_ON',
            'SET_FAN_SPEED_PERCENT',
            'SET_PURIFIER_SPEED',
            'SET_WATERING_SCHEDULE',

        ];

        // Try to find actions in priority order
        foreach ($setPriority as $actionName) {
            foreach ($actions as $action) {
                if ($action->value === strtolower($actionName)) {
                    return $action;
                }
            }
        }

        // If no prioritized action found, look for any SET_ action
        return array_find($actions, fn ($action) => str_starts_with($action->value, 'set_'));
    }
}
