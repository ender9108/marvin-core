<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use Marvin\Location\Application\Command\Zone\DeleteZone;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteZoneHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(DeleteZone $command): void
    {
        $zone = $this->zoneRepository->byId($command->zoneId);

        if ($this->zoneRepository->hasChildren($zone->id)) {
            $childrenCount = $zone->childrens->count();
            $this->logger->info('Cannot delete zone with children', ['zoneId' => $command->zoneId, 'childrenCount' => $childrenCount]);

            throw InvalidZoneHierarchy::cannotDeleteZoneWithChildren(
                $zone->zoneName,
                $childrenCount
            );
        }

        $zone->delete();
        $this->zoneRepository->remove($zone);

        $this->logger->info('Zone deleted', ['zoneId' => $command->zoneId]);
    }
}
