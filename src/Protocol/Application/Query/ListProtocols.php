<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Query;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;

final readonly class ListProtocols implements QueryInterface
{
    public function __construct(
        public ?string $type = null,
        public ?string $status = null,
    ) {
    }
}
