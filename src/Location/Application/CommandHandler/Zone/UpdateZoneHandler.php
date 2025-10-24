<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Command\CommandHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Location\Application\Command\Zone\UpdateZone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Label;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateZoneHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(UpdateZone $command): string
    {
        $zone = $this->zoneRepository->byId($command->zoneId);

        if ($command->label !== null) {
            $zone->updateLabel(new Label($command->label));
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
