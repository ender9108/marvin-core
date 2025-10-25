<?php

namespace Marvin\Secret\Application\QueryHandler;

use Marvin\Secret\Application\Query\GetSecretsByCategory;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetSecretsByCategoryHandler
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
    ) {
    }

    /**
     * @return Secret[]
     */
    public function __invoke(GetSecretsByCategory $query): array
    {
        return $this->secretRepository->byCategory($query->category);
    }
}
