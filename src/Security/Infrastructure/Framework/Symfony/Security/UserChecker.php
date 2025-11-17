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

use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Override;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserChecker implements UserCheckerInterface
{
    #[Override]
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof SecurityUser) {
            if (UserStatus::ENABLED !== UserStatus::from($user->status)) {
                throw UserNotFound::withEmail($user->email);
            }

            if (UserType::APP !== UserType::from($user->type)) {
                throw UserNotFound::withEmail($user->email);
            }
        }
    }

    public function checkPostAuth(UserInterface $user/*, TokenInterface $token*/): void
    {
    }
}
