<?php

namespace Marvin\Domotic\Application\Command\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Domotic\Domain\ValueObject\Area;
use Marvin\Domotic\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;

final readonly class CreateZone implements SyncCommandInterface
{
    public function __construct(
        public Label $label,
        public Area $area,
        public ?ZoneId $parentZone
    ) {
    }
}
