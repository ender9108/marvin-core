<?php

namespace App\System\Domain\Repository;

use App\System\Domain\Model\UserType;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface UserTypeRepositoryInterface extends RepositoryInterface
{
    public function add(UserType $userType): void;

    public function remove(UserType $userType): void;

    public function getById(int $id): ?UserType;

    public function getByReference(string $reference): ?UserType;
}
