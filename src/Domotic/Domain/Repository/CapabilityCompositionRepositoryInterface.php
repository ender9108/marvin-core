<?php

namespace Marvin\Domotic\Domain\Repository;

use Marvin\Domotic\Domain\Model\CapabilityComposition;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityCompositionId;

interface CapabilityCompositionRepositoryInterface
{
    public function save(CapabilityComposition $model, bool $flush = true): void;

    public function remove(CapabilityComposition $model, bool $flush = true): void;

    public function byId(CapabilityCompositionId $id): CapabilityComposition;
}
