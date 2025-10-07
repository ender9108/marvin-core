<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityActionId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

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
