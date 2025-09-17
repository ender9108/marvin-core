<?php

namespace Marvin\Security\Domain\Repository;

use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\ValueObject\Identity\UserStatusId;
use Marvin\Shared\Domain\ValueObject\Reference;

interface UserStatusRepositoryInterface
{
    public function save(UserStatus $userStatus, bool $flush = true): void;

    public function remove(UserStatus $userStatus, bool $flush = true): void;

    public function byId(UserStatusId $id): UserStatus;

    public function byReference(Reference $reference): UserStatus;
}
