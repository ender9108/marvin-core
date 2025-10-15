<?php

namespace Marvin\Domotic\Domain\Repository;

use Marvin\Domotic\Domain\Model\DeviceAction;
use Marvin\Domotic\Domain\ValueObject\Identity\DeviceActionId;

interface DeviceActionRepositoryInterface
{
    public function save(DeviceAction $model, bool $flush = true): void;

    public function remove(DeviceAction $model, bool $flush = true): void;

    public function byId(DeviceActionId $id): DeviceAction;
}
