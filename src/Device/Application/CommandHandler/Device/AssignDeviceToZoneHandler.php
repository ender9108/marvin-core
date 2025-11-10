<?php

declare(strict_types=1);

namespace Marvin\Device\Application\CommandHandler\Device;

use Marvin\Device\Application\Command\Device\AssignDeviceToZone;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for AssignDeviceToZone command
 *
 * Assigns a device to a specific zone
 */
#[AsMessageHandler]
final readonly class AssignDeviceToZoneHandler
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(AssignDeviceToZone $command): void
    {
        $this->logger->debug('Assigning device to zone', [
            'deviceId' => $command->deviceId->toString(),
            'zoneId' => $command->zoneId->toString(),
        ]);

        $device = $this->deviceRepository->byId($command->deviceId);

        // Assign to zone
        $device->assignToZone($command->zoneId);

        // Save device
        $this->deviceRepository->save($device);

        $this->logger->info('Device assigned to zone', [
            'deviceId' => $device->id->toString(),
            'label' => $device->label->value,
            'zoneId' => $command->zoneId->toString(),
        ]);
    }
}
