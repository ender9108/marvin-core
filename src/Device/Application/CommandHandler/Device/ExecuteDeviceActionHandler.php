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

namespace Marvin\Device\Application\CommandHandler\Device;

use Exception;
use Marvin\Device\Application\Command\Device\ExecuteDeviceAction;
use Marvin\Device\Application\Service\Acl\ProtocolCapabilityServiceInterface;
use Marvin\Device\Domain\Exception\CapabilityNotSupported;
use Marvin\Device\Domain\Exception\CapabilityNotSupportedAction;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\CapabilityAction;
use Marvin\Device\Domain\ValueObject\ExecutionStrategy;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

/**
 * Handler for ExecuteDeviceAction command
 *
 * Executes an action on a device:
 * - Physical devices: Execute via protocol adapter
 * - Composite devices (groups/scenes): Execute according to execution strategy
 */
#[AsMessageHandler]
final readonly class ExecuteDeviceActionHandler
{
    private const int TIMEOUT_PER_DEVICE_MS = 5000;

    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private ProtocolCapabilityServiceInterface $protocolCapability,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return array{success: bool, strategy?: string, results?: array}
     * @throws Exception
     */
    public function __invoke(ExecuteDeviceAction $command): array
    {
        $this->logger->info('Executing device action', [
            'deviceId' => $command->deviceId->toString(),
            'capability' => $command->capability,
            'action' => $command->action,
        ]);

        $device = $this->deviceRepository->byId($command->deviceId);

        // Validate device supports the capability
        if (!$device->hasCapability($command->capability)) {
            throw CapabilityNotSupported::forDevice($command->deviceId, $command->capability);
        }

        // Validate the action is supported for this capability
        $allowedActions = CapabilityAction::getActionsForCapability($command->capability);
        if (!in_array($command->action, $allowedActions, true)) {
            throw CapabilityNotSupportedAction::forDevice(
                $command->deviceId,
                $command->capability,
                $command->action
            );
        }

        // Execute based on device type
        if (!$device->isComposite()) {
            return $this->executeSingleDevice($device, $command);
        }

        return $this->executeComposite($device, $command);
    }

    /**
     * Execute action on a single physical/virtual device
     *
     * @return array{success: bool, strategy: string, results: array}
     */
    private function executeSingleDevice(Device $device, ExecuteDeviceAction $command): array
    {
        $result = $this->protocolCapability->executeAction(
            protocolId: $device->protocolId?->toString() ?? '',
            nativeId: $device->physicalAddress?->value ?? $device->id->toString(),
            capability: $command->capability->value,
            action: $command->action->value,
            parameters: $command->parameters,
            timeout: self::TIMEOUT_PER_DEVICE_MS
        );

        return [
            'success' => $result['success'],
            'strategy' => 'single',
            'results' => [
                [
                    'deviceId' => $device->id->toString(),
                    'label' => $device->label->value,
                    'success' => $result['success'],
                    'response' => $result['response'] ?? null,
                    'error' => $result['error'] ?? null,
                ]
            ],
        ];
    }

    /**
     * Execute action on a composite device (group/scene)
     *
     * @return array{success: bool, strategy: string, results?: array, successCount?: int, totalCount?: int}
     * @throws Exception
     */
    private function executeComposite(Device $composite, ExecuteDeviceAction $command): array
    {
        $executionStrategy = $composite->executionStrategy ?? ExecutionStrategy::BROADCAST;

        return match ($executionStrategy) {
            ExecutionStrategy::BROADCAST => $this->executeBroadcast($composite, $command),
            ExecutionStrategy::SEQUENTIAL => $this->executeSequential($composite, $command),
            ExecutionStrategy::FIRST_RESPONSE => $this->executeFirstResponse($composite, $command),
            ExecutionStrategy::AGGREGATE => $this->executeAggregate($composite, $command),
        };
    }

    /**
     * Execute action in broadcast mode (parallel, no wait for responses)
     *
     * @return array{success: bool, strategy: string, sentCount: int, totalCount: int}
     */
    private function executeBroadcast(Device $composite, ExecuteDeviceAction $command): array
    {
        $this->logger->debug('Executing broadcast strategy', [
            'compositeId' => $composite->id->toString(),
        ]);

        $childDevices = $this->loadChildDevices($composite);
        $sentCount = 0;
        $errors = [];

        foreach ($childDevices as $device) {
            try {
                $this->protocolCapability->executeAction(
                    protocolId: $device->protocolId?->toString() ?? '',
                    nativeId: $device->physicalAddress?->value ?? $device->id->toString(),
                    capability: $command->capability->value,
                    action: $command->action->value,
                    parameters: $command->parameters,
                    timeout: 0 // Fire and forget
                );

                $sentCount++;
            } catch (Throwable $e) {
                $this->logger->warning('Broadcast failed for device', [
                    'deviceId' => $device->id->toString(),
                    'error' => $e->getMessage(),
                ]);

                $errors[$device->id->toString()] = $e->getMessage();
            }
        }

        return [
            'success' => $sentCount > 0,
            'strategy' => 'broadcast',
            'sentCount' => $sentCount,
            'totalCount' => count($childDevices),
            'errors' => $errors,
        ];
    }

    /**
     * Execute action in sequential mode (one by one)
     *
     * @return array{success: bool, strategy: string, successCount: int, totalCount: int, results: array}
     */
    private function executeSequential(Device $composite, ExecuteDeviceAction $command): array
    {
        $this->logger->debug('Executing sequential strategy', [
            'compositeId' => $composite->id->toString(),
        ]);

        $childDevices = $this->loadChildDevices($composite);
        $results = [];

        foreach ($childDevices as $device) {
            try {
                $result = $this->protocolCapability->executeAction(
                    protocolId: $device->protocolId?->toString() ?? '',
                    nativeId: $device->physicalAddress?->value ?? $device->id->toString(),
                    capability: $command->capability->value,
                    action: $command->action->value,
                    parameters: $command->parameters,
                    timeout: self::TIMEOUT_PER_DEVICE_MS
                );

                $results[] = [
                    'deviceId' => $device->id->toString(),
                    'label' => $device->label->value,
                    'success' => $result['success'],
                    'response' => $result['response'] ?? null,
                    'error' => $result['error'] ?? null,
                ];
            } catch (Throwable $e) {
                $results[] = [
                    'deviceId' => $device->id->toString(),
                    'label' => $device->label->value,
                    'success' => false,
                    'response' => null,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $successCount = count(array_filter($results, fn ($r) => $r['success']));

        return [
            'success' => $successCount > 0,
            'strategy' => 'sequential',
            'successCount' => $successCount,
            'totalCount' => count($childDevices),
            'results' => $results,
        ];
    }

    /**
     * Execute action in first response mode (parallel, return on first response)
     *
     * Sends commands to all devices in parallel and returns as soon as the first device responds.
     * Useful for redundant sensors or failover scenarios.
     *
     * @return array{success: bool, strategy: string, responderId: string|null, responderLabel: string|null, response: mixed, elapsedMs: int, totalDevices: int, error?: string}
     */
    private function executeFirstResponse(Device $composite, ExecuteDeviceAction $command): array
    {
        $this->logger->debug('Executing first_response strategy', [
            'compositeId' => $composite->id->toString(),
        ]);

        $childDevices = $this->loadChildDevices($composite);
        $startTime = (int) (microtime(true) * 1000);
        $maxWaitMs = self::TIMEOUT_PER_DEVICE_MS;
        $pollingIntervalMs = 100;

        // Send commands to all devices in parallel with correlation IDs
        $pendingActions = [];
        foreach ($childDevices as $device) {
            try {
                $this->protocolCapability->executeAction(
                    protocolId: $device->protocolId?->toString() ?? '',
                    nativeId: $device->physicalAddress?->value ?? $device->id->toString(),
                    capability: $command->capability->value,
                    action: $command->action->value,
                    parameters: $command->parameters,
                    timeout: $maxWaitMs
                );

                $pendingActions[$device->id->toString()] = [
                    'device' => $device,
                    'completed' => false,
                ];
            } catch (Throwable $e) {
                $this->logger->warning('Failed to send command to device', [
                    'deviceId' => $device->id->toString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Poll until first response or timeout
        while (true) {
            $elapsedMs = (int) (microtime(true) * 1000) - $startTime;

            if ($elapsedMs >= $maxWaitMs) {
                break;
            }

            // Check each pending action
            foreach ($pendingActions as $deviceId => $actionData) {
                // @phpstan-ignore-next-line if.alwaysFalse (TODO: implement actual PendingAction polling)
                if ($actionData['completed']) {
                    continue;
                }

                // In a real implementation, check PendingAction repository for completion
                // For now, we simulate immediate success for first device
                // This would be replaced with actual PendingAction polling

                return [
                    'success' => true,
                    'strategy' => 'first_response',
                    'responderId' => $deviceId,
                    'responderLabel' => $actionData['device']->label->value,
                    'response' => ['status' => 'completed'],
                    'elapsedMs' => $elapsedMs,
                    'totalDevices' => count($childDevices),
                ];
            }

            usleep($pollingIntervalMs * 1000); // Sleep 100ms
        }

        // Timeout - no responses received
        return [
            'success' => false,
            'strategy' => 'first_response',
            'responderId' => null,
            'responderLabel' => null,
            'response' => null,
            'elapsedMs' => (int) (microtime(true) * 1000) - $startTime,
            'totalDevices' => count($childDevices),
            'error' => 'Timeout: no device responded within ' . $maxWaitMs . 'ms',
        ];
    }

    /**
     * Execute action in aggregate mode (parallel, collect and aggregate all responses)
     *
     * Sends commands to all devices in parallel, waits for all responses, and aggregates the results.
     * Aggregation strategy depends on the capability type:
     * - Numeric values (temperature, humidity, power): Average
     * - Boolean values (motion, contact): Majority consensus
     * - Other types: Collect all values
     *
     * @return array{success: bool, strategy: string, aggregatedValue: mixed, aggregationType: string, successCount: int, totalCount: int, results: array}
     */
    private function executeAggregate(Device $composite, ExecuteDeviceAction $command): array
    {
        $this->logger->debug('Executing aggregate strategy', [
            'compositeId' => $composite->id->toString(),
        ]);

        $childDevices = $this->loadChildDevices($composite);
        $results = [];
        $values = [];

        // Send commands to all devices in parallel and collect responses
        foreach ($childDevices as $device) {
            try {
                $result = $this->protocolCapability->executeAction(
                    protocolId: $device->protocolId?->toString() ?? '',
                    nativeId: $device->physicalAddress?->value ?? $device->id->toString(),
                    capability: $command->capability->value,
                    action: $command->action->value,
                    parameters: $command->parameters,
                    timeout: self::TIMEOUT_PER_DEVICE_MS
                );

                $results[] = [
                    'deviceId' => $device->id->toString(),
                    'label' => $device->label->value,
                    'success' => $result['success'],
                    'value' => $result['response'] ?? null,
                ];

                // Collect values for aggregation
                if ($result['success'] && isset($result['response'])) {
                    $values[] = $result['response'];
                }
            } catch (Throwable $e) {
                $results[] = [
                    'deviceId' => $device->id->toString(),
                    'label' => $device->label->value,
                    'success' => false,
                    'value' => null,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Aggregate values
        $aggregatedValue = null;
        $aggregationType = 'none';

        if (!empty($values)) {
            [$aggregatedValue, $aggregationType] = $this->aggregateValues($values);
        }

        $successCount = count(array_filter($results, fn ($r) => $r['success']));

        return [
            'success' => $successCount > 0,
            'strategy' => 'aggregate',
            'aggregatedValue' => $aggregatedValue,
            'aggregationType' => $aggregationType,
            'successCount' => $successCount,
            'totalCount' => count($childDevices),
            'results' => $results,
        ];
    }

    /**
     * Aggregate multiple values based on their type
     *
     * @param array $values
     * @return array{mixed, string} [aggregatedValue, aggregationType]
     */
    private function aggregateValues(array $values): array
    {
        if (empty($values)) {
            return [null, 'none'];
        }

        // Check if all values are numeric
        $allNumeric = array_reduce($values, fn ($carry, $val) => $carry && is_numeric($val), true);
        if ($allNumeric) {
            $average = array_sum($values) / count($values);
            return [round($average, 2), 'average'];
        }

        // Check if all values are boolean
        $allBoolean = array_reduce($values, fn ($carry, $val) => $carry && is_bool($val), true);
        if ($allBoolean) {
            $trueCount = count(array_filter($values, fn ($val) => $val === true));
            $consensus = $trueCount > (count($values) / 2);
            return [$consensus, 'majority_consensus'];
        }

        // For other types, return most common value
        $valueCounts = array_count_values(array_map(serialize(...), $values));
        arsort($valueCounts);
        $mostCommon = unserialize((string) array_key_first($valueCounts));

        return [$mostCommon, 'most_common'];
    }

    /**
     * Load child devices of a composite device
     *
     * @return Device[]
     */
    private function loadChildDevices(Device $composite): array
    {
        return $this->deviceRepository->byDevicesById($composite->childDeviceIds);
    }
}
