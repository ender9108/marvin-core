<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\System\Domain\ValueObject\Identity\WorkerId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class WorkerIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'worker_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return WorkerId::class;
    }
}
