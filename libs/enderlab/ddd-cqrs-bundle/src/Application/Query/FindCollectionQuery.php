<?php

namespace EnderLab\DddCqrsBundle\Application\Query;

class FindCollectionQuery implements QueryInterface
{
    public function __construct(
        public string $className,
        public ?int $page = null,
        public ?int $itemsPerPage = null,
    ) {
    }
}
