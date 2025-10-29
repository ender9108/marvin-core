<?php

namespace Marvin\Location\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

interface ZoneRepositoryInterface
{
    public function save(Zone $zone): void;
    public function remove(Zone $zone): void;
    public function all(): array;
    public function byId(ZoneId $id): Zone;
    public function byDeviceId(DeviceId $deviceId): ?Zone;
    public function bySlug(string $slug): ?Zone;
    public function byType(ZoneType $type): array;
    public function byParentZoneId(?ZoneId $parentZoneId): array;
    public function getRootZones(): array;
    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface;
}
