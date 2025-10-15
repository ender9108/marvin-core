<?php

namespace Marvin\Tests\Factory\System;

use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\System\Domain\Model\Container;
use Marvin\System\Domain\ValueObject\ContainerImage;
use Marvin\System\Domain\ValueObject\DockerImage;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ContainerFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Container::class;
    }

    protected function defaults(): array|callable
    {
        $name = self::faker()->unique()->slug();

        return [
            'id' => ContainerId::fromString(self::faker()->uuid()),
            'name' => new Label($name),
            'dockerName' => 'marvin_' . $name,
            'image' => new ContainerImage('nginx:latest'),
            'type' => self::faker()->randomElement(['protocol', 'database', 'broker']),
            'allowedActions' => ['start', 'stop', 'restart', 'logs'],
        ];
    }

    public function zigbee(): self
    {
        return $this->with([
            'name' => new Label('zigbee2mqtt'),
            'dockerName' => 'marvin_zigbee2mqtt',
            'image' => new ContainerImage('koenkk/zigbee2mqtt:latest'),
            'type' => 'protocol',
            'metadata' => new Metadata(['protocol' => 'zigbee']),
        ]);
    }
}
