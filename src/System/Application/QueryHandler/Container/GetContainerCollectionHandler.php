<?php

namespace Marvin\System\Application\QueryHandler\Container;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\System\Application\Query\Container\GetContainerCollection;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetContainerCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private ContainerRepositoryInterface $containerRepository,
    ) {
    }

    public function __invoke(GetContainerCollection $query): PaginatorInterface
    {
        return $this->containerRepository->collection(
            $query->criteria,
            $query->orderBy,
            $query->page,
            $query->itemsPerPage
        );
    }
}
