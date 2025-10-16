<?php

namespace Marvin\System\Application\QueryHandler\ActionRequest;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\System\Application\Query\ActionRequest\GetTimeoutActionRequestCollection;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetTimeoutActionRequestCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private ActionRequestRepositoryInterface $actionRequestRepository,
    ) {
    }

    public function __invoke(GetTimeoutActionRequestCollection $query): PaginatorInterface
    {
        return $this->actionRequestRepository->getTimeoutActions(
            $query->timeout,
            $query->page,
            $query->itemsPerPage,
        );
    }
}
