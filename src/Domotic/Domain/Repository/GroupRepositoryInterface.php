<?php

namespace App\Domotic\Domain\Repository;

use App\Domotic\Domain\Model\Group;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface GroupRepositoryInterface extends RepositoryInterface
{
    public function add(Group $group): void;

    public function remove(Group $group): void;

    public function byId(string|int $id): ?Group;
}
