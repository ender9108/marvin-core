<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;

final readonly class EnableUser implements SyncCommandInterface
{
    public function __construct(
        public UserId $id,
    ) {
    }
}
