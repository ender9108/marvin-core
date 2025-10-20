<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

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
