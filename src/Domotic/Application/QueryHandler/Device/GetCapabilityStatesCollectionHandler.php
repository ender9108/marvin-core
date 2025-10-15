<?php


namespace Marvin\Domotic\Application\QueryHandler\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Domotic\Application\Query\Device\GetCapabilityStateCollection;
use Marvin\Domotic\Domain\Repository\CapabilityStateRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetCapabilityStatesCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly CapabilityStateRepositoryInterface $capabilityStateRepository,
    ) {
    }

    public function __invoke(GetCapabilityStateCollection $query): PaginatorInterface
    {
        return $this->capabilityStateRepository->collection(
            $query->criteria,
            $query->orderBy,
            $query->page,
            $query->itemsPerPage,
        );
    }
}
