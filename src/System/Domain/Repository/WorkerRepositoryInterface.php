<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

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
