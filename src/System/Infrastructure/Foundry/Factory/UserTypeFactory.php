<?php

namespace App\System\Infrastructure\Foundry\Factory;

use App\System\Domain\Model\UserType;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class UserTypeFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'system.user.type.application', 'reference' => UserType::TYPE_APPLICATION],
        ['label' => 'system.user.type.system', 'reference' => UserType::TYPE_SYSTEM],
        ['label' => 'system.user.type.command', 'reference' => UserType::TYPE_COMMAND],
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
        return UserType::class;
    }
}
