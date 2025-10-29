<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Shared\Domain\ValueObject\Email;

final readonly class RequestResetPasswordUser implements CommandInterface
{
    public function __construct(
        public Email $email,
    ) {
    }
}
