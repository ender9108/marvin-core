<?php

namespace Marvin\Location\Application\QueryHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Location\Application\Query\Zone\GetZonesCollection;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetZonesCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    public function __invoke(GetZonesCollection $query): array
    {
        return $this->zoneRepository->all();
    }
}
