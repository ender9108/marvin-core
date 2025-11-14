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

namespace Marvin\Security\Infrastructure\Framework\Symfony\Service;

use Marvin\Security\Domain\Exception\LastUserAdmin;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\LastUserAdminVerifierInterface;

final readonly class LastUserAdminVerifier implements LastUserAdminVerifierInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function verify(User $user): void
    {
        if ($user->roles->isUser()) {
            return;
        }

        $countSameRoleUsers = $this->userRepository->countSameEnabledUserType($user);

        if ($countSameRoleUsers === 1) {
            throw new LastUserAdmin('You cannot delete the last admin.');
        }
    }
}
