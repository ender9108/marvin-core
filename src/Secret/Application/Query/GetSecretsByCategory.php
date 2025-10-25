<?php

namespace Marvin\Secret\Application\Query;

use Marvin\Secret\Domain\ValueObject\SecretCategory;

final readonly class GetSecretsByCategory
{
    public function __construct(
        public SecretCategory $category,
    ) {
    }
}
