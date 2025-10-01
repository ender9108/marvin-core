<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\System\Domain\ValueObject\Identity\DockerCommandId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class DockerCommandIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'docker_command_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return DockerCommandId::class;
    }
}
