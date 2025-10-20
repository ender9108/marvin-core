<?php

namespace Marvin\Location\Application\Query\Zone;

use Marvin\Location\Domain\ValueObject\ZoneType;
use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final readonly class GetZonesCollection implements QueryInterface
{
    public function __construct(
        public ?ZoneType $type = null,
        public ?ZoneId $parentZoneId = null,
        /** @param array<string, string> $orderBy */
        public array $orderBy = [],
        public int $page = 1,
        public int $itemsPerPage = 50,
    ) {}
}
