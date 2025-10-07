<?php

namespace Marvin\Domotic\Application\CommandHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Domotic\Application\Command\Zone\CreateZone;
use Marvin\Domotic\Domain\Model\Zone;
use Marvin\Domotic\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateZoneHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    public function __invoke(CreateZone $command): void
    {
        $parentZone = null;

        if (null !== $command->parentZone) {
            $parentZone = $this->zoneRepository->byId($command->parentZone);
        }

        $zone = new Zone(
            $command->label,
            $command->area,
            $parentZone
        );

        $this->zoneRepository->save($zone);
    }
}
