<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Domotic\Domain\ValueObject\Identity\ProtocolStatusId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class ProtocolStatusIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'protocol_status_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return ProtocolStatusId::class;
    }
}
