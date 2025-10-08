<?php

namespace App\Domotic\Infrastructure\DataFixtures\Foundry\Factory;

use Marvin\Domotic\Domain\List\CapabilityReference;
use Marvin\Domotic\Domain\Model\Capability;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'domotic.capabilities.switchable', 'reference' => CapabilityReference::SWITCHABLE->value],
        ['label' => 'domotic.capabilities.dimmable', 'reference' => CapabilityReference::DIMMABLE->value],
        ['label' => 'domotic.capabilities.colorable', 'reference' => CapabilityReference::COLORABLE->value],
        ['label' => 'domotic.capabilities.thermostat', 'reference' => CapabilityReference::THERMOSTAT->value],
        ['label' => 'domotic.capabilities.coverable', 'reference' => CapabilityReference::COVERABLE->value],
        ['label' => 'domotic.capabilities.fan', 'reference' => CapabilityReference::FAN->value],
        ['label' => 'domotic.capabilities.lockable', 'reference' => CapabilityReference::LOCKABLE->value],
        ['label' => 'domotic.capabilities.presence', 'reference' => CapabilityReference::PRESENCE->value],
        ['label' => 'domotic.capabilities.contact', 'reference' => CapabilityReference::CONTACT->value],
        ['label' => 'domotic.capabilities.vibration', 'reference' => CapabilityReference::VIBRATION->value],
        ['label' => 'domotic.capabilities.temperature', 'reference' => CapabilityReference::TEMPERATURE->value],
        ['label' => 'domotic.capabilities.humidity', 'reference' => CapabilityReference::HUMIDITY->value],
        ['label' => 'domotic.capabilities.illuminance', 'reference' => CapabilityReference::ILLUMINANCE->value],
        ['label' => 'domotic.capabilities.air_quality', 'reference' => CapabilityReference::AIR_QUALITY->value],
        ['label' => 'domotic.capabilities.pm2_5', 'reference' => CapabilityReference::PM2_5->value],
        ['label' => 'domotic.capabilities.pm10', 'reference' => CapabilityReference::PM10->value],
        ['label' => 'domotic.capabilities.voc_index', 'reference' => CapabilityReference::VOC_INDEX->value],
        ['label' => 'domotic.capabilities.co2', 'reference' => CapabilityReference::CO2->value],
        ['label' => 'domotic.capabilities.smoke', 'reference' => CapabilityReference::SMOKE->value],
        ['label' => 'domotic.capabilities.water_leak', 'reference' => CapabilityReference::WATER_LEAK->value],
        ['label' => 'domotic.capabilities.sound', 'reference' => CapabilityReference::SOUND->value],
        ['label' => 'domotic.capabilities.power', 'reference' => CapabilityReference::POWER->value],
        ['label' => 'domotic.capabilities.energy', 'reference' => CapabilityReference::ENERGY->value],
        ['label' => 'domotic.capabilities.voltage', 'reference' => CapabilityReference::VOLTAGE->value],
        ['label' => 'domotic.capabilities.current', 'reference' => CapabilityReference::CURRENT->value],
        ['label' => 'domotic.capabilities.battery', 'reference' => CapabilityReference::BATTERY->value],
        ['label' => 'domotic.capabilities.button', 'reference' => CapabilityReference::BUTTON->value],
        ['label' => 'domotic.capabilities.scene', 'reference' => CapabilityReference::SCENE->value],
        ['label' => 'domotic.capabilities.notify', 'reference' => CapabilityReference::NOTIFY->value],
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
                $parameters['reference'] = new Reference($parameters['reference']);
                return $parameters;
            })
        ;
    }

    public static function class(): string
    {
        return Capability::class;
    }
}
