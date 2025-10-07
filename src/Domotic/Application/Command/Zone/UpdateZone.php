<?php

namespace Marvin\Domotic\Application\Command\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Domotic\Domain\ValueObject\Area;
use Marvin\Domotic\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;

final readonly class UpdateZone implements SyncCommandInterface
{
    public function __construct(
        public ZoneId $id,
        public ?Label $label = null,
        public ?Area $area = null,
        public ?ZoneId $parentZone = null
    ) {
    }
}
