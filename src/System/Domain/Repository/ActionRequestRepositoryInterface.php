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
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\ValueObject\Identity\ActionRequestId;

interface ActionRequestRepositoryInterface
{
    public function save(ActionRequest $model, bool $flush = true): void;

    public function remove(ActionRequest $model, bool $flush = true): void;

    public function byId(ActionRequestId $id): ActionRequest;

    public function byCorrelationId(UniqId $correlationId): ActionRequest;

    public function getPendingActions(): array;

    public function getTimeoutActions(int $timeoutSeconds = 300, int $page = 0, int $itemsPerPage = 50): PaginatorInterface;

    public function collection(array $filters = [], array $orderBy = [], int $page = 0, int $itemsPerPage = 50): PaginatorInterface;
}
