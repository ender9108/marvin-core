<?php
namespace Marvin\Security\Domain\Repository;

use Marvin\Security\Domain\Model\LoginAttempt;
use Marvin\Security\Domain\Model\User;

interface LoginAttemptRepositoryInterface
{
    public function save(LoginAttempt $loginAttempt, bool $flush = true): void;

    public function remove(LoginAttempt $loginAttempt, bool $flush = true): void;

    public function countBy(User $user): int;

    public function deleteBy(User $user): void;
}
