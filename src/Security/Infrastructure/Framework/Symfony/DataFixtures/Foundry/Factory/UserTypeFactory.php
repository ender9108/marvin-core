<?php
namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Security\Domain\Model\UserType;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class UserTypeFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'system.user.type.application', 'reference' => UserType::TYPE_APPLICATION],
        ['label' => 'system.user.type.system', 'reference' => UserType::TYPE_SYSTEM],
        ['label' => 'system.user.type.cli', 'reference' => UserType::TYPE_CLI],
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
        return UserType::class;
    }
}
