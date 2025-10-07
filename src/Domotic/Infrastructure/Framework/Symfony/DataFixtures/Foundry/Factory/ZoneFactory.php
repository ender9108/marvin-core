<?php

namespace App\Domotic\Infrastructure\DataFixtures\Foundry\Factory;

use Marvin\Domotic\Domain\Model\Zone;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class ZoneFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'Home', 'area' => 0],
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
