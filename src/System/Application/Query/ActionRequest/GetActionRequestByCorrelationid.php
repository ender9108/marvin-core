<?php

namespace Marvin\System\Application\Query\ActionRequest;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;

final readonly class GetActionRequestByCorrelationid implements QueryInterface
{
    public function __construct(
        public UniqId $correlationId,
    ) {
    }
}
