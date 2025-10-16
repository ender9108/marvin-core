<?php

namespace Marvin\System\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\System\Domain\Model\Container;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

interface ContainerRepositoryInterface
{
    public function save(Container $model, bool $flush = true): void;

    public function remove(Container $model, bool $flush = true): void;

    public function byId(ContainerId $id): Container;

    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface;
}
