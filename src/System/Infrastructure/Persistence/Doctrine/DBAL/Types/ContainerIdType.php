<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class ContainerIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'container_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return ContainerId::class;
    }
}
