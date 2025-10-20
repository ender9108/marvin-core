<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;

final readonly class ChangeEmailUser implements SyncCommandInterface
{
    public function __construct(
        public UserId $id,
        public Email $email,
    ) {
    }
}
