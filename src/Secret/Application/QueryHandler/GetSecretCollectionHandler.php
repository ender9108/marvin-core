<?php

namespace Marvin\Secret\Application\QueryHandler;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Secret\Application\Query\GetSecretCollection;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetSecretCollectionHandler
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
    ) {}

    public function __invoke(GetSecretCollection $query): PaginatorInterface
    {
        return $this->secretRepository->collection(
            $query->filters,
            $query->orderBy,
            $query->page,
            $query->itemsPerPage,
        );
    }
}
