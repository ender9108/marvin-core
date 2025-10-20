<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Location\Domain\ValueObject\ZonePath;
use Marvin\Location\Application\Command\Zone\MoveZone;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Psr\Log\LoggerInterface;

final readonly class MoveZoneHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(MoveZone $command): string
    {
        $zone = $this->zoneRepository->byId($command->zoneId);

        if ($command->newParentZoneId !== null && $command->newParentZoneId === $command->zoneId) {
            throw InvalidZoneHierarchy::circularReference($zone->label);
        }

        $newParentZone = null;
        $newParentZoneId = null;

        if ($command->newParentZoneId !== null) {
            $newParentZone = $this->zoneRepository->byId($command->newParentZoneId);

            if (!$newParentZone->type->canHaveChildren()) {
                throw InvalidZoneHierarchy::cannotHaveChildren(
                    $newParentZone->label,
                    $newParentZone->type
                );
            }

            /** @var Zone[] $descendants */
            $descendants = $this->zoneRepository->getDescendants($zone->id);

            foreach ($descendants as $descendant) {
                if ($descendant->id->equals($command->newParentZoneId)) {
                    throw InvalidZoneHierarchy::circularReference($zone->label);
                }
            }

            $newParentZoneId = $newParentZone->id;
        }

        $zone->moveToParent($newParentZoneId);

        if ($newParentZone !== null) {
            $newPath = $newParentZone->path->append($zone->label);
        } else {
            $newPath = new ZonePath($zone->label);
        }

        $zone->updatePath($newPath);
        $this->zoneRepository->save($zone);
        $this->logger->info('Zone moved', ['zoneId' => $command->zoneId]);

        return $command->zoneId;
    }
}

