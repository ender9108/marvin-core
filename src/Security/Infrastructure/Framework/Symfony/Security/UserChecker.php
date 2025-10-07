<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Security;

use Marvin\Security\Domain\Exception\UserIsDeleted;
use Marvin\Security\Domain\Exception\UserIsDisabled;
use Marvin\Security\Domain\Exception\UserIsLocaked;
use Override;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserChecker implements UserCheckerInterface
{
    #[Override]
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof SecurityUser) {
            match (true) {
                $user->status->isDeleted() => throw new UserIsDeleted(),
                $user->status->isDisabled() => throw new UserIsDisabled(),
                $user->status->isLocked() => throw new UserIsLocaked(),
            };
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
