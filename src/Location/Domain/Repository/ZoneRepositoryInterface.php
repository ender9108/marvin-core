<?php

namespace Marvin\Location\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;

interface ZoneRepositoryInterface
{
    public function save(Zone $zone): void;
    public function remove(Zone $zone): void;
    public function byId(ZoneId $id): Zone;
    public function byLabel(Label $label): ?Zone;
    public function bySlug(string $slug): ?Zone;
    public function all(): array;
    public function byType(ZoneType $type): array;
    public function byParentZoneId(?ZoneId $parentZoneId): array;
    public function getRootZones(): array;
    public function getHierarchy(): array;
    public function countChildren(ZoneId $zoneId): int;
    public function hasChildren(ZoneId $zoneId): bool;
    public function getDescendants(ZoneId $zoneId): array;
    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface;
}
