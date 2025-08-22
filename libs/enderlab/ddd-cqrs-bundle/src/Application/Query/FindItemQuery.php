<?php

namespace EnderLab\DddCqrsBundle\Application\Query;

class FindItemQuery implements QueryInterface
{
    public function __construct(
        public int $id,
        public string $className,
    ) {
    }
}
