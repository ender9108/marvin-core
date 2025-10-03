<?php

namespace Marvin\Domotic\Domain\Repository;

use Marvin\Domotic\Domain\Model\Zone;
use Marvin\Domotic\Domain\ValueObject\Identity\ZoneId;

interface ZoneRepositoryInterface
{
    public function save(Zone $model, bool $flush = true): void;

    public function remove(Zone $model, bool $flush = true): void;

    public function byId(ZoneId $id): Zone;
}
