<?php

namespace App\Domotic\Infrastructure\DataFixtures\Foundry\Factory;

use Marvin\Domotic\Domain\List\ProtocolStatusReference;
use Marvin\Domotic\Domain\Model\ProtocolStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class ProtocolStatusFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'domotic.protocol.status.enabled', 'reference' => ProtocolStatusReference::STATUS_ENABLED->value],
        ['label' => 'domotic.protocol.status.disabled', 'reference' => ProtocolStatusReference::STATUS_DISABLED->value],
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
