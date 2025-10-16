<?php

namespace Marvin\System\Application\Query\Container;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;

final readonly class GetContainerCollection implements QueryInterface
{
    public function __construct(
        /** @param array<string, string> $criteria */
        public array $criteria = [],
        /** @param array<string, string> $orderBy */
        public array $orderBy = [],
        public int $page = 1,
        public int $itemsPerPage = 50,
    ) {
    }
}
