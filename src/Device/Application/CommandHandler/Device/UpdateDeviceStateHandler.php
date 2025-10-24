<?php

namespace Marvin\Device\Application\CommandHandler\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Device\Application\Command\Device\UpdateDeviceState;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateDeviceStateHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(UpdateDeviceState $command): void
    {
        $this->logger->debug('Updating device state', [
            'deviceId' => $command->deviceId,
            'capability' => $command->capability->value,
            'value' => $command->value,
        ]);

        $device = $this->deviceRepository->byId($command->deviceId);

        $device->updateState($command->capability, $command->value, $command->unit);

        $this->deviceRepository->save($device);

        $this->logger->info('Device state updated', [
            'deviceId' => $device->id->toString(),
            'capability' => $command->capability,
        ]);
    }
}
