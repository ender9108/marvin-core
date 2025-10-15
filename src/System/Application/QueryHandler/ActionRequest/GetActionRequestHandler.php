<?php

namespace Marvin\System\Application\QueryHandler\ActionRequest;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\System\Application\Query\ActionRequest\GetActionRequest;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetActionRequestHandler implements QueryHandlerInterface
{
    public function __construct(
        private ActionRequestRepositoryInterface $actionRequestRepository,
    ) {
    }

    public function __invoke(GetActionRequest $query): ActionRequest
    {
        return $this->actionRequestRepository->byId($query->id);
    }
}
