<?php

namespace Marvin\Device\Application\EventHandler;

use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerInterface;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Location\Domain\Event\Zone\ZoneDeleted;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Psr\Log\LoggerInterface;

final readonly class ZoneDeletedHandler implements DomainEventHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(ZoneDeleted $event): void
    {
        $this->logger->info('Zone deleted, unassigning devices', [
            'zoneId' => $event->zoneId,
        ]);

        $zoneId = new ZoneId($event->zoneId);
        $devices = $this->deviceRepository->byZoneId($zoneId);

        foreach ($devices as $device) {
            $device->unassignFromZone();
            $this->deviceRepository->save($device);
        }

        $this->logger->info('Devices unassigned from deleted zone', [
            'zoneId' => $event->zoneId,
            'affectedDevices' => count($devices),
        ]);
    }
}
