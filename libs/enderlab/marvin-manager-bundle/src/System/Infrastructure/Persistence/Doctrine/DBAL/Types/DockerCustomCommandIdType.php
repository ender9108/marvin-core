<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\Persistence\Doctrine\DBAL\Types;

use App\System\Domain\ValueObject\Identity\DockerCustomCommandId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class DockerCustomCommandIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'docker_custom_command_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return DockerCustomCommandId::class;
    }
}
