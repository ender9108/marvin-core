<?php

namespace Marvin\Domotic\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Domotic\Domain\Model\CapabilityAction;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityActionId;

interface CapabilityActionRepositoryInterface
{
    public function save(CapabilityAction $model, bool $flush = true): void;

    public function remove(CapabilityAction $model, bool $flush = true): void;

    public function byId(CapabilityActionId $id): CapabilityAction;

    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface;
}
