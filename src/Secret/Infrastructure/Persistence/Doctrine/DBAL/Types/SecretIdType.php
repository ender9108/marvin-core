<?php

namespace Marvin\Secret\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Secret\Domain\ValueObject\Identity\SecretId;
use Override;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class SecretIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'secret_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return SecretId::class;
    }
}
