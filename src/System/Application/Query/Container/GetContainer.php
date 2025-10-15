<?php

namespace Marvin\System\Application\Query\Container;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

final readonly class GetContainer implements QueryInterface
{
    public function __construct(
        public ContainerId $id,
    ) {
    }
}
