<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Symfony\Bridge\Doctrine\Types\AbstractUidType;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityCompositionId;

final class CapabilityCompositionIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'capability_composition_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return CapabilityCompositionId::class;
    }
}
