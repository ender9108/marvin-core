<?php

namespace Marvin\Device\Application\CommandHandler\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Device\Application\Command\Device\DeleteDevice;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteDeviceHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {}

    public function __invoke(DeleteDevice $command): void
    {
        $this->logger->info('Deleting device', [
            'deviceId' => $command->deviceId,
        ]);

        $device = $this->deviceRepository->byId($command->deviceId);
        $deviceName = $device->label->value;

        $device->delete();

        $this->deviceRepository->remove($device);

        $this->logger->info('Device deleted', [
            'deviceId' => $command->deviceId,
            'name' => $deviceName,
        ]);
    }
}

