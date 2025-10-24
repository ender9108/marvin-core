<?php

namespace Marvin\Device\Domain\Repository;

use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Model\DeviceCapability;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

interface DeviceCapabilityRepositoryInterface
{
    public function save(DeviceCapability $capability): void;

    public function remove(DeviceCapability $capability): void;

    public function all(): array;

    public function byId(DeviceId $id): DeviceCapability;

    public function byDeviceId(DeviceId $id): array;
}
