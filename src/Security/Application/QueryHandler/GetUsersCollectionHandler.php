<?php

namespace Marvin\Security\Application\QueryHandler;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Security\Application\Query\GetUsersCollection;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetUsersCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(GetUsersCollection $query): PaginatorInterface
    {
        return $this->userRepository->collection(
            $query->criteria,
            $query->orderBy,
            $query->page,
            $query->itemsPerPage,
        );
    }
}
