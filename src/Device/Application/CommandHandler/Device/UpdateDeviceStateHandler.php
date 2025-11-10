<?php

declare(strict_types=1);

namespace Marvin\Device\Application\CommandHandler\Device;

use Marvin\Device\Application\Command\Device\UpdateDeviceState;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for UpdateDeviceState command
 *
 * Updates device state and emits DeviceStateChanged event
 * Used primarily for state synchronization from Protocol Context
 */
#[AsMessageHandler]
final readonly class UpdateDeviceStateHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(UpdateDeviceState $command): void
    {
        $this->logger->debug('Updating device state', [
            'deviceId' => $command->deviceId->toString(),
            'newState' => $command->newState,
        ]);

        $device = $this->deviceRepository->byId($command->deviceId);

        // Update device state (will emit DeviceStateChanged event if there are changes)
        $device->updateState($command->newState);

        // Save device
        $this->deviceRepository->save($device);

        $this->logger->debug('Device state updated', [
            'deviceId' => $device->id->toString(),
            'currentState' => $device->getCurrentState(),
        ]);
    }
}
