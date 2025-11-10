<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\System\Domain\ValueObject\Identity\ActionRequestId;
use Override;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class ActionRequestIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'action_request_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return ActionRequestId::class;
    }
}
