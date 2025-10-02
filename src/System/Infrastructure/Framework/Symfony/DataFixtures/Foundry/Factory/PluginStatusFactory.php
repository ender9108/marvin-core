<?php

namespace Marvin\System\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\List\PluginStatusReference;
use Marvin\System\Domain\Model\PluginStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class PluginStatusFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'system.plugin.status.enabled', 'reference' => PluginStatusReference::STATUS_ENABLED->value],
        ['label' => 'system.plugin.status.disabled', 'reference' => PluginStatusReference::STATUS_DISABLED->value],
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
        return PluginStatus::class;
    }
}
