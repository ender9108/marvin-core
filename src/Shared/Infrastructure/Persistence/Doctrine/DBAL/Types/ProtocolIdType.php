<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Override;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class ProtocolIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'protocol_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return ProtocolId::class;
    }
}
