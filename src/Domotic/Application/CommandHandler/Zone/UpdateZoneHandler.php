<?php

namespace Marvin\Domotic\Application\CommandHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Domotic\Application\Command\Zone\UpdateZone;
use Marvin\Domotic\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateZoneHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository
    ) {
    }

    public function __invoke(UpdateZone $command): void
    {
        $zone = $this->zoneRepository->byId($command->id);
        $zone->update(
            $command->label,
            $command->area,
            $command->parentZone,
        );

        $this->zoneRepository->save($zone);
    }
}
