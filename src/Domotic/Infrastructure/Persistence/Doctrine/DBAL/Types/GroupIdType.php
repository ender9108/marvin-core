<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Domotic\Domain\ValueObject\Identity\GroupId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class GroupIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'group_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return GroupId::class;
    }
}
