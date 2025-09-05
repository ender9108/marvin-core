<?php
namespace Marvin\Security\Application\Command\User;

use Marvin\Security\Domain\ValueObject\Identity\UserId;
use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;

final readonly class RegisterUserLoginAttempt implements CommandInterface
{
    public function __construct(
        public UserId $id,
    ) {
    }
}
