<?php

namespace Marvin\Location\Application\Command\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final readonly class MoveZone implements SyncCommandInterface
{
    public function __construct(
        public ZoneId $zoneId,
        public ?ZoneId $newParentZoneId,
    ) {
    }
}
