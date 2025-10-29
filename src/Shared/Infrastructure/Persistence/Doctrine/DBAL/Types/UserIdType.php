<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Override;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class UserIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'user_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return UserId::class;
    }
}
