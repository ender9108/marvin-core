<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use Marvin\Location\Application\Command\Zone\MoveZone;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
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
            throw InvalidZoneHierarchy::circularReference($zone->zoneName);
        }

        $newParentZone = null;

        if ($command->newParentZoneId !== null) {
            /** @var Zone $newParentZone */
            $newParentZone = $this->zoneRepository->byId($command->newParentZoneId);

            if (!$newParentZone->type->canHaveChildren()) {
                throw InvalidZoneHierarchy::cannotHaveChildren(
                    $newParentZone->zoneName,
                    $newParentZone->type
                );
            }

            foreach ($zone->childrens as $children) {
                if ($children->id->equals($command->newParentZoneId)) {
                    throw InvalidZoneHierarchy::circularReference($zone->zoneName);
                }
            }
        }

        $zone->move($newParentZone);
        $this->zoneRepository->save($zone);
        $this->logger->info('Zone moved', ['zoneId' => $command->zoneId]);

        return $command->zoneId;
    }
}
