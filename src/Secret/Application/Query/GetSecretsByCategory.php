<?php

namespace Marvin\Secret\Application\Query;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Secret\Domain\ValueObject\SecretCategory;

final readonly class GetSecretsByCategory implements QueryInterface
{
    public function __construct(
        public SecretCategory $category,
    ) {
    }
}
