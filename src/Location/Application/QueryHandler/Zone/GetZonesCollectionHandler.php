<?php

namespace Marvin\Location\Application\QueryHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
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

    public function __invoke(GetZonesCollection $query): PaginatorInterface
    {
        $filters = [];

        if ($query->type !== null) {
            $filters['type'] = $query->type;
        }

        if ($query->parentZoneId !== null) {
            $filters['parent'] = $query->parentZoneId;
        }

        return $this->zoneRepository->collection($filters, $query->orderBy, $query->page, $query->itemsPerPage);
    }
}
