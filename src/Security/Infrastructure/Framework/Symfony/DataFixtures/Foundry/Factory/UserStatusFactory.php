<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class UserStatusFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'security.user.status.enabled', 'reference' => UserStatus::STATUS_ENABLED,],
        ['label' => 'security.user.status.disabled', 'reference' => UserStatus::STATUS_DISABLED,],
        ['label' => 'security.user.status.locked', 'reference' => UserStatus::STATUS_LOCKED],
        ['label' => 'security.user.status.to_delete', 'reference' => UserStatus::STATUS_TO_DELETE],
    ];

    protected function defaults(): array|callable
    {
        return [];
    }

    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function(array $parameters): array {
                $parameters['label'] = new Label($parameters['label']);
                $parameters['reference'] = new Reference($parameters['reference']);

                return $parameters;
            })
        ;
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
