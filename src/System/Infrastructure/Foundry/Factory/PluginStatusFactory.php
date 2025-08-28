<?php

namespace App\System\Infrastructure\Foundry\Factory;

use App\System\Domain\Model\PluginStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class PluginStatusFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'system.plugin.status.enabled', 'reference' => PluginStatus::STATUS_ENABLED],
        ['label' => 'system.plugin.status.disabled', 'reference' => PluginStatus::STATUS_DISABLED],
        ['label' => 'system.plugin.status.to_delete', 'reference' => PluginStatus::STATUS_TO_DELETE],
    ];

    protected function defaults(): array|callable
    {
        return [];
    }

    public static function getDatas(): array
    {
        return self::$datas;
    }

    public static function class(): string
    {
        return PluginStatus::class;
    }
}
