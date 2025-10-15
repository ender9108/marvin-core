<?php

namespace Marvin\Domotic\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Domotic\Domain\Model\CapabilityState;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityStateId;

interface CapabilityStateRepositoryInterface
{
    public function save(CapabilityState $model, bool $flush = true): void;

    public function remove(CapabilityState $model, bool $flush = true): void;

    public function byId(CapabilityStateId $id): CapabilityState;

    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface;
}
