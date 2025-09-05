<?php
namespace Marvin\Security\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Security\Domain\ValueObject\Identity\UserStatusId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class UserStatusIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'user_status_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return UserStatusId::class;
    }
}
