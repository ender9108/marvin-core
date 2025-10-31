<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Exception\ZoneAlreadyExists;
use Marvin\Location\Domain\Exception\ZoneParentNotFound;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
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
        if ($this->zoneRepository->bySlug($this->slugger->slugify($command->zoneName->value)) !== null) {
            throw ZoneAlreadyExists::withLabel($command->zoneName);
        }

        $parentZone = null;

        if ($command->parentZoneId !== null) {
            try {
                $parentZone = $this->zoneRepository->byId($command->parentZoneId);
            } catch (DomainException $de) {
                throw ZoneParentNotFound::withId($command->parentZoneId);
            }

            if (!$parentZone->type->canHaveChildren()) {
                throw InvalidZoneHierarchy::cannotHaveChildren(
                    $parentZone->zoneName,
                    $parentZone->type
                );
            }
        }

        $zone = new Zone(
            zoneName: $command->zoneName,
            type: $command->type,
            targetTemperature: $command->targetTemperature,
            targetPowerConsumption: $command->targetPowerConsumption,
            targetHumidity: $command->targetHumidity,
            icon: $command->icon,
            surfaceArea: $command->surfaceArea,
            orientation: $command->orientation,
            color: $command->color,
            metadata: $command->metadata,
        );

        $zone->updateSlug($this->slugger);

        if ($parentZone !== null) {
            $zone->move($parentZone);
        }

        $this->zoneRepository->save($zone);
        $this->logger->info('Zone created', ['zoneId' => $zone->id->toString()]);

        return $zone->id->toString();
    }
}
