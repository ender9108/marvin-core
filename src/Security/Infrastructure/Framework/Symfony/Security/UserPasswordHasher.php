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

namespace Marvin\Security\Infrastructure\Framework\Symfony\Security;

use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Override;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Override]
    public function hash(User $user, string $password): string
    {
        return $this->passwordHasher->hashPassword(SecurityUser::create($user), $password);
    }

    #[Override]
    public function verify(User $user, string $password): bool
    {
        return $this->passwordHasher->isPasswordValid(SecurityUser::create($user), $password);
    }
}
