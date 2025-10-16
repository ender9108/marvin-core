<?php

namespace Marvin\System\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\System\Domain\Model\Worker;
use Marvin\System\Domain\ValueObject\Identity\WorkerId;

interface WorkerRepositoryInterface
{
    public function save(Worker $model, bool $flush = true): void;

    public function remove(Worker $model, bool $flush = true): void;

    public function byId(WorkerId $id): Worker;

    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface;
}
