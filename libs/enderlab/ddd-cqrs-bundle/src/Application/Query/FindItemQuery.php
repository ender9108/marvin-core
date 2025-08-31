<?php

namespace EnderLab\DddCqrsBundle\Application\Query;

class FindItemQuery implements QueryInterface
{
    public function __construct(
        public int|string $id,
        public string $className,
    ) {
    }
}
