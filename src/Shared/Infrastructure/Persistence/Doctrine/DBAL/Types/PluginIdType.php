<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Override;
use Marvin\Shared\Domain\ValueObject\Identity\PluginId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class PluginIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'plugin_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return PluginId::class;
    }
}
