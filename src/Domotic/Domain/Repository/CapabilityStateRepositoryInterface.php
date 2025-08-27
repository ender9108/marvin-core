<?php

namespace App\Domotic\Domain\Repository;

use App\Domotic\Domain\Model\CapabilityState;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface CapabilityStateRepositoryInterface extends RepositoryInterface
{
    public function add(CapabilityState $capabilityState): void;

    public function remove(CapabilityState $capabilityState): void;

    public function byId(string $id): ?CapabilityState;
}
