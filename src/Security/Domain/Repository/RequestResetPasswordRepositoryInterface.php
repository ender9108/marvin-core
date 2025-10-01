<?php

namespace Marvin\Security\Domain\Repository;

use Marvin\Security\Domain\Model\RequestResetPassword;
use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordIdType;

interface RequestResetPasswordRepositoryInterface
{
    public function save(RequestResetPassword $request, bool $flush = true): void;

    public function remove(RequestResetPassword $request, bool $flush = true): void;

    public function byId(RequestResetPasswordIdType $id): RequestResetPassword;

    public function byToken(string $token): RequestResetPassword;
}
