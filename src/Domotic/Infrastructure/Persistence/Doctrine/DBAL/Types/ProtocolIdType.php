<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Symfony\Bridge\Doctrine\Types\AbstractUidType;
use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolId;

final class ProtocolIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'protocol_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return ProtocolId::class;
    }
}
