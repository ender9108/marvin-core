<?php

namespace Marvin\Security\Domain\Service;

use Marvin\Security\Domain\Model\User;

interface PasswordHasherInterface
{
    public function hash(User $user, string $password): string;

    public function verify(User $user, string $password): bool;
}
