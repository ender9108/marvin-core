<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

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
