<?php

namespace Marvin\Device\Application\Query\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final readonly class GetDevicesByZone implements QueryInterface
{
    public function __construct(
        public ZoneId $zoneId
    ) {}
}
