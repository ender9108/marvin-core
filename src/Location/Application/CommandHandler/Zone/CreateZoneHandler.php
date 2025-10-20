<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Location\Domain\ValueObject\ZonePath;
use EnderLab\DddCqrsBundle\Application\Command\CommandHandlerInterface;
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Exception\ZoneAlreadyExists;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Label;
use Psr\Log\LoggerInterface;

final readonly class CreateZoneHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(CreateZone $command): string
    {
        if ($this->zoneRepository->byLabel($command->label) !== null) {
            throw ZoneAlreadyExists::withLabel($command->label);
        }

        $parentZoneId = null;
        $parentZone = null;

        if ($command->parentZoneId !== null) {
            $parentZone = $this->zoneRepository->find($command->parentZoneId);
            if ($parentZone === null) {
                throw InvalidZoneHierarchy::parentNotFound($command->parentZoneId);
            }
            if (!$parentZone->getType()->canHaveChildren()) {
                throw InvalidZoneHierarchy::cannotHaveChildren(
                    $parentZone->label->value,
                    $parentZone->getType()->value
                );
            }
            $parentZoneId = $parentZone->getId();
        }

        $zone = new Zone(
            label: new Label($command->label),
            type: $command->type,
            targetTemperature: $command->targetTemperature,
            targetPowerConsumption: $command->targetPowerConsumption,
            icon: $command->icon,
            parentZoneId: $parentZoneId,
            surfaceArea: $command->surfaceArea,
            orientation: $command->orientation,
            color: $command->color,
            metadata: $command->metadata,
        );

        if ($parentZone !== null) {
            $zonePath = $parentZone->getPath()->append($command->label);
        } else {
            $zonePath = new ZonePath($command->label);
        }

        $zone->updatePath($zonePath);

        $this->zoneRepository->save($zone);
        $this->logger->info('Zone created', ['zoneId' => $zone->id->toString()]);

        return $zone->id->toString();
    }
}

