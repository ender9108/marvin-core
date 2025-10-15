<?php


namespace Marvin\Domotic\Application\QueryHandler\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Domotic\Application\Query\Device\GetCapabilitiesCollection;
use Marvin\Domotic\Domain\Repository\CapabilityRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetCapabilitiesCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly CapabilityRepositoryInterface $capabilityRepository,
    ) {
    }

    public function __invoke(GetCapabilitiesCollection $query): PaginatorInterface
    {
        return $this->capabilityRepository->collection(
            $query->criteria,
            $query->orderBy,
            $query->page,
            $query->itemsPerPage,
        );
    }
}
