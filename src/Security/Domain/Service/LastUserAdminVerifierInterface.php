<?php

namespace Marvin\Security\Domain\Service;

use Marvin\Security\Domain\Exception\LastUserAdmin;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;

interface LastUserAdminVerifierInterface
{
    public function verify(User $user): void;
}
