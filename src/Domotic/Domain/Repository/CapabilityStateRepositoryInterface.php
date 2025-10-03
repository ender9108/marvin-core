<?php

namespace Marvin\Domotic\Domain\Repository;

use Marvin\Domotic\Domain\Model\CapabilityState;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityStateId;

interface CapabilityStateRepositoryInterface
{
    public function save(CapabilityState $model, bool $flush = true): void;

    public function remove(CapabilityState $model, bool $flush = true): void;

    public function byId(CapabilityStateId $id): CapabilityState;
}
