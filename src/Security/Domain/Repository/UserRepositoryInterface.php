<?php
namespace Marvin\Security\Domain\Repository;

use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Email;

interface UserRepositoryInterface
{
    public function save(User $user, bool $flush = true): void;

    public function remove(User $user, bool $flush = true): void;

    public function countSameEnabledUserType(User $user): int;

    public function byId(UserId $userId): User;

    public function byEmail(Email $email): User;

    public function byIdentifier(string $identifier): User;
}
