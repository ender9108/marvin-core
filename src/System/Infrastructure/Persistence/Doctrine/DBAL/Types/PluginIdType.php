<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Symfony\Bridge\Doctrine\Types\AbstractUidType;
use Marvin\System\Domain\ValueObject\Identity\PluginId;

final class PluginIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'plugin_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return PluginId::class;
    }
}
