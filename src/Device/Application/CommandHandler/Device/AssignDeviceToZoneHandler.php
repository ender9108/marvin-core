<?php

namespace Marvin\Device\Application\CommandHandler\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Device\Application\Command\Device\AssignDeviceToZone;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AssignDeviceToZoneHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(AssignDeviceToZone $command): void
    {
        $this->logger->info('Assigning device to zone', [
            'deviceId' => $command->deviceId,
            'zoneId' => $command->zoneId,
        ]);

        $device = $this->deviceRepository->byId($command->deviceId);

        if ($command->zoneId === null) {
            $device->unassignFromZone();
        } else {
            $device->assignToZone($command->zoneId);
        }

        $this->deviceRepository->save($device);

        $this->logger->info('Device assigned to zone', [
            'deviceId' => $device->id->toString(),
            'zoneId' => $command->zoneId,
        ]);
    }
}
