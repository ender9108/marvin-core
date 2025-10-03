<?php

namespace Marvin\Domotic\Domain\Repository;

use Marvin\Domotic\Domain\Model\Group;
use Marvin\Domotic\Domain\ValueObject\Identity\GroupId;

interface GroupRepositoryInterface
{
    public function save(Group $model, bool $flush = true): void;

    public function remove(Group $model, bool $flush = true): void;

    public function byId(GroupId $id): Group;
}
