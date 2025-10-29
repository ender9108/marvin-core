<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Override;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class UniqIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'uniq_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return UniqId::class;
    }
}
