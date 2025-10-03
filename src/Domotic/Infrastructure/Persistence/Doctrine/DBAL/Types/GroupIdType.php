<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Symfony\Bridge\Doctrine\Types\AbstractUidType;
use Marvin\Domotic\Domain\ValueObject\Identity\GroupId;

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
