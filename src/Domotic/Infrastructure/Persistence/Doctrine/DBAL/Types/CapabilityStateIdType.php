<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Symfony\Bridge\Doctrine\Types\AbstractUidType;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityStateId;

final class CapabilityStateIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'capability_state_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return CapabilityStateId::class;
    }
}
