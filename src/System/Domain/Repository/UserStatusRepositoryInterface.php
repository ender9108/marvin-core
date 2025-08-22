<?php

namespace App\System\Domain\Repository;

use App\System\Domain\Model\UserStatus;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface UserStatusRepositoryInterface extends RepositoryInterface
{
    public function add(UserStatus $userStatus): void;

    public function remove(UserStatus $userStatus): void;

    public function getById(int $id): ?UserStatus;

    public function getByReference(string $reference): ?UserStatus;
}
