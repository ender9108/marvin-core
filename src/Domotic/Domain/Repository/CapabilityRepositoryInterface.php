<?php

namespace App\Domotic\Domain\Repository;

use App\Domotic\Domain\Model\Capability;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface CapabilityRepositoryInterface extends RepositoryInterface
{
    public function add(Capability $capability): void;

    public function remove(Capability $capability): void;

    public function byId(string $id): ?Capability;
}
