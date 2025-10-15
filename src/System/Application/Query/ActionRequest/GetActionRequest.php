<?php

namespace Marvin\System\Application\Query\ActionRequest;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\System\Domain\ValueObject\Identity\ActionRequestId;

final readonly class GetActionRequest implements QueryInterface
{
    public function __construct(
        public ActionRequestId $id,
    ) {
    }
}
