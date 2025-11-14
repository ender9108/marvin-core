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

namespace Marvin\Security\Domain\Service;

use Marvin\Security\Domain\Model\User;

interface PasswordHasherInterface
{
    public function hash(User $user, string $password): string;

    public function verify(User $user, string $password): bool;
}
