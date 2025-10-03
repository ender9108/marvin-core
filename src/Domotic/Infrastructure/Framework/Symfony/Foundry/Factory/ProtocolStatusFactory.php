<?php

namespace App\Domotic\Infrastructure\Foundry\Factory;

use App\Domotic\Domain\Model\ProtocolStatus;
use App\System\Domain\Model\UserStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class ProtocolStatusFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'domotic.protocol.status.enabled', 'reference' => ProtocolStatus::STATUS_ENABLED],
        ['label' => 'domotic.protocol.status.disabled', 'reference' => ProtocolStatus::STATUS_DISABLED],
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
        return ProtocolStatus::class;
    }
}
