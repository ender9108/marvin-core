<?php

namespace Marvin\Security\Domain\Repository;

use Marvin\Security\Domain\Model\UserType;
use Marvin\Security\Domain\ValueObject\Identity\UserTypeId;
use Marvin\Shared\Domain\ValueObject\Reference;

interface UserTypeRepositoryInterface
{
    public function save(UserType $userType, bool $flush = true): void;

    public function remove(UserType $userType, bool $flush = true): void;

    public function byId(UserTypeId $id): UserType;

    public function byReference(Reference $reference): UserType;
}
