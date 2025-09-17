<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Security;

use Marvin\Security\Domain\Exception\UserIsDeleted;
use Marvin\Security\Domain\Exception\UserIsDisabled;
use Marvin\Security\Domain\Exception\UserIsLocaked;
use Marvin\Security\Domain\Model\UserStatus;
use Override;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserChecker implements UserCheckerInterface
{
    #[Override]
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof SecurityUser) {
            switch ($user->status) {
                case UserStatus::STATUS_LOCKED:
                    throw new UserIsLocaked();
                case UserStatus::STATUS_DISABLED:
                    throw new UserIsDisabled();
                case UserStatus::STATUS_TO_DELETE:
                    throw new UserIsDeleted();
            }
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
