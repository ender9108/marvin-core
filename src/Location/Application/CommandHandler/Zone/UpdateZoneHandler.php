<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use Marvin\Location\Application\Command\Zone\UpdateZone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateZoneHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(UpdateZone $command): string
    {
        $zone = $this->zoneRepository->byId($command->zoneId);

        if ($command->zoneName !== null) {
            $zone->updateZoneName(ZoneName::fromString($command->zoneName));
        }

        $zone->updateConfiguration(
            surfaceArea: $command->surfaceArea,
            orientation: $command->orientation,
            targetTemperature: $command->targetTemperature,
            targetPowerConsumption: $command->targetPowerConsumption,
            icon: $command->icon,
            color: $command->color,
        );

        $this->zoneRepository->save($zone);
        $this->logger->info('Zone updated', ['zoneId' => $command->zoneId]);

        return $command->zoneId;
    }
}
