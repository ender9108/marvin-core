<?php

namespace App\System\Domain\Repository;

use App\System\Domain\Model\User;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function add(User $user): void;

    public function remove(User $user): void;

    public function getById(int $id): ?User;

    public function byIdentifier(string $email): ?User;
}
