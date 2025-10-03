<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Symfony\Bridge\Doctrine\Types\AbstractUidType;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityActionId;

final class CapabilityActionIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'capability_action_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return CapabilityActionId::class;
    }
}
