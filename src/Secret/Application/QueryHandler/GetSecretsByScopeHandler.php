<?php

namespace Marvin\Secret\Application\QueryHandler;

use Marvin\Secret\Application\Query\GetSecretsByScope;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetSecretsByScopeHandler
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
    ) {
    }

    /**
     * @return Secret[]
     */
    public function __invoke(GetSecretsByScope $query): array
    {
        return $this->secretRepository->byScope($query->scope);
    }
}
