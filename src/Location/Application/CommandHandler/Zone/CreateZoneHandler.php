<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Exception\ZoneAlreadyExists;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Location\Domain\ValueObject\ZonePath;
use Marvin\Shared\Domain\Service\SluggerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateZoneHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private SluggerInterface $slugger,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateZone $command): string
    {
        if ($this->zoneRepository->bySlug($command->label->value) !== null) {
            throw ZoneAlreadyExists::withLabel($command->label);
        }

        $parentZone = null;

        if ($command->parentZoneId !== null) {
            $parentZone = $this->zoneRepository->byId($command->parentZoneId);

            if (!$parentZone->type->canHaveChildren()) {
                throw InvalidZoneHierarchy::cannotHaveChildren(
                    $parentZone->label,
                    $parentZone->type
                );
            }
        }

        $zone = new Zone(
            type: $command->type,
            targetTemperature: $command->targetTemperature,
            targetPowerConsumption: $command->targetPowerConsumption,
            icon: $command->icon,
            surfaceArea: $command->surfaceArea,
            orientation: $command->orientation,
            color: $command->color,
            metadata: $command->metadata,
        );

        $zone->updateLabel($command->label, $this->slugger);

        if ($parentZone !== null) {
            $zone->moveToParent($parentZone);
            $zonePath = $parentZone->path->append($zone->slug);
        } else {
            $zonePath = new ZonePath($zone->slug);
        }

        $zone->updatePath($zonePath);

        $this->zoneRepository->save($zone);
        $this->logger->info('Zone created', ['zoneId' => $zone->id->toString()]);

        return $zone->id->toString();
    }
}
