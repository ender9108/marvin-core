<?php

namespace Marvin\Location\Application\QueryHandler\Zone;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Location\Application\Query\Zone\GetZoneById;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetZoneByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    public function __invoke(GetZoneById $query): Zone
    {
        return $this->zoneRepository->byId($query->zoneId);
    }
}
