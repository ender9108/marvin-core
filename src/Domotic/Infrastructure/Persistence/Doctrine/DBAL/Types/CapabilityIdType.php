<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class CapabilityIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'capability_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return CapabilityId::class;
    }
}
