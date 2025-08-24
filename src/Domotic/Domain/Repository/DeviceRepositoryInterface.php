<?php

namespace App\Domotic\Domain\Repository;

use App\Domotic\Domain\Model\Device;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface DeviceRepositoryInterface extends RepositoryInterface
{
    public function add(Device $device): void;

    public function remove(Device $device): void;

    public function byId(string|int $id): ?Device;
}
