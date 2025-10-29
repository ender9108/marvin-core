<?php

namespace Marvin\Security\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Override;
use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordId as RequestResetPasswordId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class RequestResetPasswordIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'request_reset_password_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return RequestResetPasswordId::class;
    }
}
