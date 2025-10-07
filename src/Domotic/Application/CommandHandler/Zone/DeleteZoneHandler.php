<?php

namespace Marvin\Domotic\Application\CommandHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Domotic\Application\Command\Zone\DeleteZone;
use Marvin\Domotic\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteZoneHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository
    ) {
    }

    public function __invoke(DeleteZone $command): void
    {
        $zone = $this->zoneRepository->byId($command->id);
        $zone->remove();

        $this->zoneRepository->remove($zone);
    }
}
