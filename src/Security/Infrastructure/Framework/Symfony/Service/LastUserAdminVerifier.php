<?php

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
            throw new LastUserAdmin();
        }
    }
}
