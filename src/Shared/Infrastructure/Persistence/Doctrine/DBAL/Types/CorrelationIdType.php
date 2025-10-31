<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Override;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class CorrelationIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'correlation_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return CorrelationId::class;
    }
}
