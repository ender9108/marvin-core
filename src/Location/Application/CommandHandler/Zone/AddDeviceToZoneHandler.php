<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use Marvin\Location\Application\Command\Zone\AddDeviceToZone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddDeviceToZoneHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    public function __invoke(AddDeviceToZone $command): void
    {
        $zone = $this->zoneRepository->byId($command->zoneId);

        $zone->addDevice($command->deviceId);
        $this->zoneRepository->save($zone);
    }
}
