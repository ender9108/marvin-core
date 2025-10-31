<?php

namespace Marvin\Device\Domain\Repository;

use Marvin\Device\Domain\Model\DeviceCapability;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

interface DeviceCapabilityRepositoryInterface
{
    public function save(DeviceCapability $capability, bool $flush = true): void;

    public function remove(DeviceCapability $capability, bool $flush = true): void;

    public function all(): array;

    public function byId(DeviceId $id): DeviceCapability;

    public function byDeviceId(DeviceId $id): array;
}
