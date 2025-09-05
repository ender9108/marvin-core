<?php
namespace Marvin\Security\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Security\Domain\ValueObject\Identity\UserTypeId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class UserTypeIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'user_type_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return UserTypeId::class;
    }
}
