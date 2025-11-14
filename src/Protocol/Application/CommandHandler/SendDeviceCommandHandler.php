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

namespace Marvin\Protocol\Application\CommandHandler;

use Marvin\Protocol\Application\Command\SendDeviceCommand;
use Marvin\Shared\Application\Service\Acl\DeviceQueryServiceInterface;
use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
final readonly class SendDeviceCommandHandler
{
    /**
     * @param iterable<ProtocolAdapterInterface> $adapters
     */
    public function __construct(
        private DeviceQueryServiceInterface $deviceQueryService,
        #[AutowireIterator(tag: 'protocol.adapter')]
        private iterable $adapters,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SendDeviceCommand $command): void
    {
        $this->logger->info('Processing SendDeviceCommand', [
            'deviceId' => $command->deviceId,
            'action' => $command->action,
        ]);

        try {
            // 1. Get device information from Device Context via ACL
            $device = $this->deviceQueryService->getDevice($command->deviceId);

            if ($device === null) {
                $this->logger->error('Device not found', [
                    'deviceId' => $command->deviceId,
                ]);
                return;
            }

            // 2. Find appropriate adapter for the device protocol
            $adapter = $this->findAdapterForProtocol($device->protocol);

            if ($adapter === null) {
                $this->logger->error('No adapter found for protocol', [
                    'protocol' => $device->protocol,
                    'deviceId' => $command->deviceId,
                ]);
                return;
            }

            // 3. Determine execution mode
            $executionMode = $command->executionMode !== null
                ? ExecutionMode::from($command->executionMode)
                : $adapter->getDefaultExecutionMode();

            $this->logger->debug('Sending command via adapter', [
                'adapter' => $adapter->getName(),
                'deviceId' => $command->deviceId,
                'nativeId' => $device->nativeId,
                'action' => $command->action,
                'executionMode' => $executionMode->value,
            ]);

            // 4. Send command via adapter
            // Note: CORRELATION_ID and DEVICE_LOCK synchronous modes are handled by
            // ProtocolCapabilityService (Device Context ACL) which manages PendingAction
            // lifecycle, polling, and timeout. SendDeviceCommandHandler is for direct
            // protocol-level command execution (typically FIRE_AND_FORGET).
            $result = $adapter->sendCommand(
                nativeId: $device->nativeId,
                action: $command->action,
                parameters: $command->parameters,
                mode: $executionMode
            );

            if ($result !== null) {
                $this->logger->info('Command executed successfully', [
                    'deviceId' => $command->deviceId,
                    'result' => $result,
                ]);
            } else {
                $this->logger->info('Command sent (FIRE_AND_FORGET mode)', [
                    'deviceId' => $command->deviceId,
                ]);
            }
        } catch (Throwable $e) {
            $this->logger->error('Error sending device command', [
                'error' => $e->getMessage(),
                'deviceId' => $command->deviceId,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function findAdapterForProtocol(string $protocol): ?ProtocolAdapterInterface
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->supportsProtocol($protocol)) {
                return $adapter;
            }
        }

        return null;
    }
}
