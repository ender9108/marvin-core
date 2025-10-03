<?php

namespace Marvin\Domotic\Domain\Repository;

use Marvin\Domotic\Domain\Model\Capability;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityId;

interface CapabilityRepositoryInterface
{
    public function save(Capability $model, bool $flush = true): void;

    public function remove(Capability $model, bool $flush = true): void;

    public function byId(CapabilityId $id): Capability;
}
