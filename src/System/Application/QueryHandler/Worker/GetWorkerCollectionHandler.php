<?php

namespace Marvin\System\Application\QueryHandler\Worker;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\System\Application\Query\Worker\GetWorkerCollection;
use Marvin\System\Domain\Repository\WorkerRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetWorkerCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private WorkerRepositoryInterface $workerRepository,
    ) {
    }

    public function __invoke(GetWorkerCollection $query): PaginatorInterface
    {
        //return $this->workerRepository->($query->id);
    }
}
