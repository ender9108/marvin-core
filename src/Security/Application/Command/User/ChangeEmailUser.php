<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Email;

final readonly class ChangeEmailUser implements SyncCommandInterface
{
    public function __construct(
        public UserId $id,
        public Email $email,
    ) {
    }
}
