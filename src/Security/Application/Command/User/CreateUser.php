<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Reference;

final readonly class CreateUser implements SyncCommandInterface
{
    public function __construct(
        public Email $email,
        public Firstname $firstname,
        public Lastname $lastname,
        public Roles $roles,
        public Reference $type,
        public string $password,
    ) {
    }
}
