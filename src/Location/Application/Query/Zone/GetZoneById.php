<?php

namespace Marvin\Location\Application\Query\Zone;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final readonly class GetZoneById implements QueryInterface
{
    public function __construct(
        public ZoneId $zoneId,
    ) {
    }
}
