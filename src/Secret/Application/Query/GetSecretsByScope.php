<?php

namespace Marvin\Secret\Application\Query;

use Marvin\Secret\Domain\ValueObject\SecretScope;

final readonly class GetSecretsByScope
{
    public function __construct(
        public SecretScope $scope,
    ) {
    }
}
