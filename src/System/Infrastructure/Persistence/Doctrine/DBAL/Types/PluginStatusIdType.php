<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\System\Domain\ValueObject\Identity\PluginStatusId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class PluginStatusIdType extends AbstractUidType
{
    #[\Override]
    public function getName(): string
    {
        return 'plugin_status_id';
    }

    #[\Override]
    protected function getUidClass(): string
    {
        return PluginStatusId::class;
    }
}
