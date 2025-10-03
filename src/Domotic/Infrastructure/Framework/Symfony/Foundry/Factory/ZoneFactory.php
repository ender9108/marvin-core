<?php

namespace App\Domotic\Infrastructure\Foundry\Factory;

use App\Domotic\Domain\Model\Capability;
use App\Domotic\Domain\Model\Zone;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class ZoneFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'Home'],
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
        return Zone::class;
    }
}
