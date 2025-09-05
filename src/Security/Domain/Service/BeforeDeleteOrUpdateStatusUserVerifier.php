<?php

namespace Marvin\Security\Domain\Service;

use Marvin\Security\Domain\Exception\LastUserType;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;

final readonly class BeforeDeleteOrUpdateStatusUserVerifier
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
            throw new LastUserType();
        }
    }
}
