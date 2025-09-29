<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Security;

use Marvin\Security\Domain\Exception\UserIsDeleted;
use Marvin\Security\Domain\Exception\UserIsDisabled;
use Marvin\Security\Domain\Exception\UserIsLocaked;
use Marvin\Security\Domain\List\UserStatusReference;
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
                case UserStatusReference::STATUS_LOCKED->value:
                    throw new UserIsLocaked();
                case UserStatusReference::STATUS_DISABLED->value:
                    throw new UserIsDisabled();
                case UserStatusReference::STATUS_TO_DELETE->value:
                    throw new UserIsDeleted();
            }
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
