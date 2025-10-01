<?php

namespace Marvin\Security\Application\Command\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;

final readonly class ResetPasswordUser implements SyncCommandInterface
{
    public function __construct(
        public string $token,
        public string $password,
    ) {
    }
}
