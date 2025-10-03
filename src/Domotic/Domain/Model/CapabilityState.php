<?php

namespace Marvin\Domotic\Domain\Model;

use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityStateId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;

final class CapabilityState
{
    public readonly CapabilityStateId $id;

    public function __construct(
        private(set) Label $label,
        private(set) Reference $reference,
        private(set) array $stateschema
    ) {
        $this->id = new CapabilityStateId();
    }
}
