<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\System\Domain\ValueObject\Identity\DockerId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class DockerIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'docker_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return DockerId::class;
    }
}
