<?php

namespace Marvin\System\Application\Query\Worker;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\System\Domain\ValueObject\Identity\WorkerId;

final readonly class GetWorker implements QueryInterface
{
    public function __construct(
        public WorkerId $id,
    ) {
    }
}
