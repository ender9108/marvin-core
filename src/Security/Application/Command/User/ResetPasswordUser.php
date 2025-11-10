<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;

final readonly class ResetPasswordUser implements CommandInterface
{
    public function __construct(
        public string $token,
        public string $password,
    ) {
    }
}
