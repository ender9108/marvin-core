<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Location\Application\Command\Zone\MoveZone;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Location\Domain\ValueObject\ZonePath;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class MoveZoneHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(MoveZone $command): string
    {
        $zone = $this->zoneRepository->byId($command->zoneId);

        if ($command->newParentZoneId !== null && $command->newParentZoneId === $command->zoneId) {
            throw InvalidZoneHierarchy::circularReference($zone->label);
        }

        $newParentZone = null;

        if ($command->newParentZoneId !== null) {
            /** @var Zone $newParentZone */
            $newParentZone = $this->zoneRepository->byId($command->newParentZoneId);

            if (!$newParentZone->type->canHaveChildren()) {
                throw InvalidZoneHierarchy::cannotHaveChildren(
                    $newParentZone->label,
                    $newParentZone->type
                );
            }

            foreach ($zone->childrens as $children) {
                if ($children->id->equals($command->newParentZoneId)) {
                    throw InvalidZoneHierarchy::circularReference($zone->label);
                }
            }
        }

        $zone->moveToParent($newParentZone);

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
