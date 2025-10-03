<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Symfony\Bridge\Doctrine\Types\AbstractUidType;
use Marvin\Domotic\Domain\ValueObject\Identity\ZoneId;

final class ZoneIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'zone_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return ZoneId::class;
    }
}
