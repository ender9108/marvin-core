<?php

namespace Marvin\Domotic\Domain\Repository;

use Marvin\Domotic\Domain\Model\Device;
use Marvin\Domotic\Domain\ValueObject\Identity\DeviceId;

interface DeviceRepositoryInterface
{
    public function save(Device $model, bool $flush = true): void;

    public function remove(Device $model, bool $flush = true): void;

    public function byId(DeviceId $id): Device;
}
