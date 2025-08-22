<?php

namespace EnderLab\DddCqrsBundle\Application\Query;

use Doctrine\ORM\EntityManagerInterface;
use EnderLab\DddCqrsBundle\Application\Query\Attribute\AsQueryHandler;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

#[AsQueryHandler]
class FindCollectionQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
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
