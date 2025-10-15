<?php

namespace Marvin\System\Application\Query\ActionRequest;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;

final readonly class GetTimeoutActionRequestCollection implements QueryInterface
{
    public function __construct(
        public int $timeout,
    ) {
    }
}
