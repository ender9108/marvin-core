<?php

namespace Marvin\Location\Application\CommandHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventBusInterface;
use Marvin\Location\Application\Command\Zone\DeleteZone;
use Marvin\Location\Domain\Event\Zone\ZoneDeleted;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteZoneHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(DeleteZone $command): void
    {
        $zone = $this->zoneRepository->byId($command->zoneId);

        if ($zone->childrens->count() > 0) {
            $childrenCount = $this->zoneRepository->countChildren($zone->id);
            $this->logger->info('Cannot delete zone with children', ['zoneId' => $command->zoneId, 'childrenCount' => $childrenCount]);
            throw InvalidZoneHierarchy::cannotDeleteZoneWithChildren(
                $zone->label,
                $childrenCount
            );
        }

        $zone->delete();
        $this->zoneRepository->remove($zone);

        $this->logger->info('Zone deleted', ['zoneId' => $command->zoneId]);
    }
}
