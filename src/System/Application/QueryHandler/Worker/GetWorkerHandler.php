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

namespace Marvin\System\Application\QueryHandler\Worker;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\System\Application\Query\Worker\GetWorker;
use Marvin\System\Domain\Model\Worker;
use Marvin\System\Domain\Repository\WorkerRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetWorkerHandler implements QueryHandlerInterface
{
    public function __construct(
        private WorkerRepositoryInterface $workerRepository,
    ) {
    }

    public function __invoke(GetWorker $query): Worker
    {
        return $this->workerRepository->byId($query->id);
    }
}
