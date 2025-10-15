<?php

namespace Marvin\System\Application\QueryHandler\ActionRequest;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\System\Application\Query\ActionRequest\GetActionRequestByCorrelationid;
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\System\Domain\Repository\ActionRequestRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetActionRequestByCorrelationIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private ActionRequestRepositoryInterface $actionRequestRepository,
    ) {
    }

    public function __invoke(GetActionRequestByCorrelationid $query): ActionRequest
    {
        return $this->actionRequestRepository->byCorrelationId($query->correlationId);
    }
}
