<?php

namespace Marvin\Security\Application\Query;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserId;

class GetUserById implements QueryInterface
{
    public function __construct(
        public UserId $id
    ) {
    }
}
