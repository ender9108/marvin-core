<?php

namespace EnderLab\DddCqrsBundle\Application\Query;

use Doctrine\ORM\EntityManagerInterface;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
readonly class FindCollectionQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(FindCollectionQuery $query): RepositoryInterface
    {
        $repository = $this->em->getRepository($query->className);

        if (
            null !== $query->page &&
            null !== $query->itemsPerPage &&
            method_exists($repository, 'withPagination')
        ) {
            $repository = $repository->withPagination($query->page, $query->itemsPerPage);
        }

        return $repository;
    }
}
