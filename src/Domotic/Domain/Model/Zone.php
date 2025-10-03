<?php

namespace Marvin\Domotic\Domain\Model;

use Marvin\Domotic\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Domotic\Domain\Model\Zone;

final class Zone
{
    public readonly ZoneId $id;

    public function __construct(
        private(set) Label $label,
        private(set) Zone $parentzone,
        private(set) float $area
    ) {
        $this->id = new ZoneId();
    }
}
