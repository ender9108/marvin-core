<?php

namespace App\Domotic\Domain\Repository;

use App\Domotic\Domain\Model\CapabilityComposition;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface CapabilityCompositionRepositoryInterface extends RepositoryInterface
{
    public function add(CapabilityComposition $capabilityComposition): void;

    public function remove(CapabilityComposition $capabilityComposition): void;

    public function byId(string $id): ?CapabilityComposition;
}
