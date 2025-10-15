<?php

namespace Marvin\System\Application\QueryHandler\ActionRequest;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\System\Application\Query\ActionRequest\GetTimeoutActionRequestCollection;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetTimeoutActionRequestCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly ActionRequestRepositoryInterface $actionRequestRepository,
    ) {
    }

    public function __invoke(GetTimeoutActionRequestCollection $query): array
    {
    }
}
