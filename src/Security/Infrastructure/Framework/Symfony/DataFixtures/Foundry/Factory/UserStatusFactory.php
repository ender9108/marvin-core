<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Security\Domain\List\UserStatusReference;
use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class UserStatusFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'security.user.status.enabled', 'reference' => UserStatusReference::STATUS_ENABLED->value, ],
        ['label' => 'security.user.status.disabled', 'reference' => UserStatusReference::STATUS_DISABLED->value, ],
        ['label' => 'security.user.status.locked', 'reference' => UserStatusReference::STATUS_LOCKED->value],
        ['label' => 'security.user.status.to_delete', 'reference' => UserStatusReference::STATUS_TO_DELETE->value],
    ];

    protected function defaults(): array|callable
    {
        return [];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function (array $parameters): array {
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
