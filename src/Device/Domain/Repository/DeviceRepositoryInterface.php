<?php

namespace Marvin\Device\Domain\Repository;

use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

interface DeviceRepositoryInterface
{
    public function save(Device $model, bool $flush = true): void;

    public function remove(Device $model, bool $flush = true): void;

    public function byId(DeviceId $id): ?Device;

    public function byIds(array $ids): array;

    public function byLabel(string $label): ?Device;

    public function byPhysicalAddress(string $physicalAddress): ?Device;

    public function byProtocolId(ProtocolId $protocolId): array;

    public function byZoneId(ZoneId $zoneId): array;

    public function byType(DeviceType $type): array;

    public function getVirtualByType(VirtualDeviceType $virtualType): array;

    public function all(): array;

    public function getComposites(): array;

    public function byCompositeId(DeviceId $compositeId): array;

    public function getGroups(): array;

    public function getGroupById(DeviceId $groupId): Device;

    public function getScenes(): array;

    public function getSceneById(DeviceId $sceneId): Device;

    public function getCompositesByChildDevice(DeviceId $childDeviceId): array;

    public function getCompositesByZone(ZoneId $zoneId): array;
}
