<?php

namespace App\System\Infrastructure\Foundry\Factory;

use App\System\Domain\Model\UserStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class UserStatusFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        [
            'label' => 'system.user.status.enabled',
            'reference' => UserStatus::STATUS_ENABLED,
        ],
        [
            'label' => 'system.user.status.disabled',
            'reference' => UserStatus::STATUS_DISABLED,
        ],
        [
            'label' => 'system.user.status.to_delete',
            'reference' => UserStatus::STATUS_TO_DELETE
        ],
        [
            'label' => 'system.user.status.deleted',
            'reference' => UserStatus::STATUS_DELETED
        ],
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
        return UserStatus::class;
    }
}
