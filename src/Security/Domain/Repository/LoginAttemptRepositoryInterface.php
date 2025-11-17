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

use Marvin\Security\Domain\Model\LoginAttempt;
use Marvin\Security\Domain\Model\User;

interface LoginAttemptRepositoryInterface
{
    public function save(LoginAttempt $loginAttempt, bool $flush = true): void;

    public function remove(LoginAttempt $loginAttempt, bool $flush = true): void;

    public function countBy(User $user): int;

    public function deleteBy(User $user): void;
}
