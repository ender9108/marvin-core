<?php

namespace Marvin\Security\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Override;
use Marvin\Security\Domain\ValueObject\Identity\LoginAttemptId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class LoginAttemptIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'login_attempt_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return LoginAttemptId::class;
    }
}
