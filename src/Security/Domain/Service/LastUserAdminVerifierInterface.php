<?php

namespace Marvin\Security\Domain\Service;

use Marvin\Security\Domain\Model\User;

interface LastUserAdminVerifierInterface
{
    public function verify(User $user): void;
}
