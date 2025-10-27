<?php

namespace Marvin\Secret\Application\Query;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;

final readonly class GetSecretCollection implements QueryInterface
{
    public function __construct(
        /** @param array<string, string> $filters */
        public array $filters = [],
        /** @param array<string, string> $orderBy */
        public array $orderBy = [],
        public int $page = 1,
        public int $itemsPerPage = 50,
    ) {
    }
}
