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

use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Email;
use Override;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<SecurityUser>
 */
final readonly class SecurityUserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    #[Override]
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    #[Override]
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->byEmail(new Email($identifier));
        if ($user === null) {
            throw new UserNotFoundException();
        }

        return SecurityUser::create($user);
    }

    #[Override]
    public function supportsClass(string $class): bool
    {
        return $class === SecurityUser::class;
    }
}
