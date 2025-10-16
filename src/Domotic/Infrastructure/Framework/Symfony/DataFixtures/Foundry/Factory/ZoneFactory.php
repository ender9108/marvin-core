<?php

namespace Marvin\Domotic\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Domotic\Domain\Model\Zone;
use Marvin\Domotic\Domain\ValueObject\Area;
use Marvin\Shared\Domain\ValueObject\Label;
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

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function (array $parameters): array {
                $parameters['label'] = new Label($parameters['label']);
                $parameters['area'] = new Area($parameters['area']);
                return $parameters;
            })
        ;
    }

    public static function class(): string
    {
        return Zone::class;
    }
}
