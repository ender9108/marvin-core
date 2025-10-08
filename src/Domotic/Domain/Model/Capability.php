<?php

namespace Marvin\Domotic\Domain\Model;

use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;

class Capability
{
    public readonly CapabilityId $id;

    public function __construct(
        private(set) Label $label,
        private(set) Reference $reference
    ) {
        $this->id = new CapabilityId();
    }
}
