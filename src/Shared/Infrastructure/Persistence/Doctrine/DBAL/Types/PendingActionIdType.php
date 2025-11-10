<?php

declare(strict_types=1);

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\Shared\Domain\ValueObject\Identity\PendingActionId;
use Override;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class PendingActionIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'pending_action_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return PendingActionId::class;
    }
}
