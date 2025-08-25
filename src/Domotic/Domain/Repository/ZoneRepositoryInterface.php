<?php

namespace App\Domotic\Domain\Repository;

use App\Domotic\Domain\Model\Zone;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface ZoneRepositoryInterface extends RepositoryInterface
{
    public function add(Zone $zone): void;

    public function remove(Zone $zone): void;

    public function byId(string $id): ?Zone;
}
