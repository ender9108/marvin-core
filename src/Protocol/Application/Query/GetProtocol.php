<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Query;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;

final readonly class GetProtocol implements QueryInterface
{
    public function __construct(
        public string $protocolId,
    ) {
    }
}
