<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;

final readonly class UpdateUserProfile implements SyncCommandInterface
{
    public function __construct(
        public UserId $id,
        public Firstname $firstname,
        public Lastname $lastname,
        public Roles $roles
    ) {
    }
}
