<?php


namespace Marvin\Domotic\Application\QueryHandler\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Domotic\Application\Query\Device\GetCapabilityActionCollection;
use Marvin\Domotic\Domain\Repository\CapabilityActionRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetCapabilityActionsCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly CapabilityActionRepositoryInterface $capabilityActionRepository,
    ) {
    }

    public function __invoke(GetCapabilityActionCollection $query): PaginatorInterface
    {
        return $this->capabilityActionRepository->collection(
            $query->criteria,
            $query->orderBy,
            $query->page,
            $query->itemsPerPage,
        );
    }
}
