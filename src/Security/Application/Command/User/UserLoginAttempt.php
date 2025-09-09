<?php
namespace Marvin\Security\Application\Command\User;

use Marvin\Security\Domain\ValueObject\Identity\UserId;
use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;

final readonly class UserLoginAttempt implements CommandInterface
{
    public function __construct(
        public UserId $id,
    ) {
    }
}
