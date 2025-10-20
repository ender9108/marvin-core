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
    ) {}

    public function __invoke(GetZonesCollection $query): PaginatorInterface
    {
        $criterias = [];

        if ($query->type !== null) {
            $criterias['type'] = $query->type;
        }

        if ($query->parentZoneId !== null) {
            $criterias['parentZoneId'] = $query->parentZoneId;
        }

        return $this->zoneRepository->collection($criterias, $query->orderBy, $query->page, $query->itemsPerPage);
    }
}
