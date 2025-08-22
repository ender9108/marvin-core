<?php

namespace App\Domotic\Infrastructure\Foundry\Factory;

use App\Domotic\Domain\Model\Capability;
use App\Domotic\Domain\ReferenceList\CapabilityReferenceList;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'domotic.capabilities.switchable', 'reference' => CapabilityReferenceList::SWITCHABLE],
        ['label' => 'domotic.capabilities.dimmable', 'reference' => CapabilityReferenceList::DIMMABLE],
        ['label' => 'domotic.capabilities.colorable', 'reference' => CapabilityReferenceList::COLORABLE],
        ['label' => 'domotic.capabilities.thermostat', 'reference' => CapabilityReferenceList::THERMOSTAT],
        ['label' => 'domotic.capabilities.coverable', 'reference' => CapabilityReferenceList::COVERABLE],
        ['label' => 'domotic.capabilities.fan', 'reference' => CapabilityReferenceList::FAN],
        ['label' => 'domotic.capabilities.lockable', 'reference' => CapabilityReferenceList::LOCKABLE],
        ['label' => 'domotic.capabilities.presence', 'reference' => CapabilityReferenceList::PRESENCE],
        ['label' => 'domotic.capabilities.contact', 'reference' => CapabilityReferenceList::CONTACT],
        ['label' => 'domotic.capabilities.vibration', 'reference' => CapabilityReferenceList::VIBRATION],
        ['label' => 'domotic.capabilities.temperature', 'reference' => CapabilityReferenceList::TEMPERATURE],
        ['label' => 'domotic.capabilities.humidity', 'reference' => CapabilityReferenceList::HUMIDITY],
        ['label' => 'domotic.capabilities.illuminance', 'reference' => CapabilityReferenceList::ILLUMINANCE],
        ['label' => 'domotic.capabilities.air_quality', 'reference' => CapabilityReferenceList::AIR_QUALITY],
        ['label' => 'domotic.capabilities.smoke', 'reference' => CapabilityReferenceList::SMOKE],
        ['label' => 'domotic.capabilities.water_leak', 'reference' => CapabilityReferenceList::WATER_LEAK],
        ['label' => 'domotic.capabilities.sound', 'reference' => CapabilityReferenceList::SOUND],
        ['label' => 'domotic.capabilities.power', 'reference' => CapabilityReferenceList::POWER],
        ['label' => 'domotic.capabilities.energy', 'reference' => CapabilityReferenceList::ENERGY],
        ['label' => 'domotic.capabilities.voltage', 'reference' => CapabilityReferenceList::VOLTAGE],
        ['label' => 'domotic.capabilities.current', 'reference' => CapabilityReferenceList::CURRENT],
        ['label' => 'domotic.capabilities.battery', 'reference' => CapabilityReferenceList::BATTERY],
        ['label' => 'domotic.capabilities.button', 'reference' => CapabilityReferenceList::BUTTON],
        ['label' => 'domotic.capabilities.scene', 'reference' => CapabilityReferenceList::SCENE],
        ['label' => 'domotic.capabilities.notify', 'reference' => CapabilityReferenceList::NOTIFY],
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
        return Capability::class;
    }
}
