<?php

namespace Marvin\Secret\Application\Query;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Secret\Domain\ValueObject\SecretScope;

final readonly class GetSecretsByScope implements QueryInterface
{
    public function __construct(
        public SecretScope $scope,
    ) {
    }
}
