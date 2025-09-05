<?php
namespace Marvin\Security\Application\Command\User;

use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Shared\Domain\ValueObject\Reference;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Email;

final readonly class CreateUser implements SyncCommandInterface
{
    public function __construct(
        public Email $email,
        public Firstname $firstName,
        public Lastname $lastName,
        public Roles $roles,
        public Reference $type,
        public string $password,
    ) {
    }
}
