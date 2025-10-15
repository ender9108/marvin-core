<?php

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
