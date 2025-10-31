<?php

namespace Marvin\Location\Application\Query\Zone;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final readonly class GetZonesCollection implements QueryInterface
{
    public function __construct(
    ) {
    }
}
