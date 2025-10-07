<?php

namespace Marvin\Security\Domain\Repository;

use Marvin\Security\Domain\Model\RequestResetPassword;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordId;

interface RequestResetPasswordRepositoryInterface
{
    public function save(RequestResetPassword $request, bool $flush = true): void;

    public function remove(RequestResetPassword $request, bool $flush = true): void;

    public function byId(RequestResetPasswordId $id): RequestResetPassword;

    public function byToken(string $token): RequestResetPassword;

    public function checkIfRequestAlreadyExists(User $user): bool;
}
