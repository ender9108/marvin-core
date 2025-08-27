<?php

namespace App\Domotic\Domain\Repository;

use App\Domotic\Domain\Model\CapabilityAction;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface CapabilityActionRepositoryInterface extends RepositoryInterface
{
    public function add(CapabilityAction $capabilityAction): void;

    public function remove(CapabilityAction $capabilityAction): void;

    public function byId(string $id): ?CapabilityAction;
}
