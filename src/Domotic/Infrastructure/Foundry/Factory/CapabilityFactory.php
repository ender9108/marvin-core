<?php

namespace App\Domotic\Infrastructure\Foundry\Factory;

use App\Domotic\Domain\Model\Capability;
use App\Domotic\Domain\ReferenceList\CapabilityReferenceList;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'domotic.capabilities.switchable', 'reference' => CapabilityReferenceList::SWITCHABLE->value],
        ['label' => 'domotic.capabilities.dimmable', 'reference' => CapabilityReferenceList::DIMMABLE->value],
        ['label' => 'domotic.capabilities.colorable', 'reference' => CapabilityReferenceList::COLORABLE->value],
        ['label' => 'domotic.capabilities.thermostat', 'reference' => CapabilityReferenceList::THERMOSTAT->value],
        ['label' => 'domotic.capabilities.coverable', 'reference' => CapabilityReferenceList::COVERABLE->value],
        ['label' => 'domotic.capabilities.fan', 'reference' => CapabilityReferenceList::FAN->value],
        ['label' => 'domotic.capabilities.lockable', 'reference' => CapabilityReferenceList::LOCKABLE->value],
        ['label' => 'domotic.capabilities.presence', 'reference' => CapabilityReferenceList::PRESENCE->value],
        ['label' => 'domotic.capabilities.contact', 'reference' => CapabilityReferenceList::CONTACT->value],
        ['label' => 'domotic.capabilities.vibration', 'reference' => CapabilityReferenceList::VIBRATION->value],
        ['label' => 'domotic.capabilities.temperature', 'reference' => CapabilityReferenceList::TEMPERATURE->value],
        ['label' => 'domotic.capabilities.humidity', 'reference' => CapabilityReferenceList::HUMIDITY->value],
        ['label' => 'domotic.capabilities.illuminance', 'reference' => CapabilityReferenceList::ILLUMINANCE->value],
        ['label' => 'domotic.capabilities.air_quality', 'reference' => CapabilityReferenceList::AIR_QUALITY->value],
        ['label' => 'domotic.capabilities.pm2_5', 'reference' => CapabilityReferenceList::PM2_5->value],
        ['label' => 'domotic.capabilities.pm10', 'reference' => CapabilityReferenceList::PM10->value],
        ['label' => 'domotic.capabilities.voc_index', 'reference' => CapabilityReferenceList::VOC_INDEX->value],
        ['label' => 'domotic.capabilities.co2', 'reference' => CapabilityReferenceList::CO2->value],
        ['label' => 'domotic.capabilities.smoke', 'reference' => CapabilityReferenceList::SMOKE->value],
        ['label' => 'domotic.capabilities.water_leak', 'reference' => CapabilityReferenceList::WATER_LEAK->value],
        ['label' => 'domotic.capabilities.sound', 'reference' => CapabilityReferenceList::SOUND->value],
        ['label' => 'domotic.capabilities.power', 'reference' => CapabilityReferenceList::POWER->value],
        ['label' => 'domotic.capabilities.energy', 'reference' => CapabilityReferenceList::ENERGY->value],
        ['label' => 'domotic.capabilities.voltage', 'reference' => CapabilityReferenceList::VOLTAGE->value],
        ['label' => 'domotic.capabilities.current', 'reference' => CapabilityReferenceList::CURRENT->value],
        ['label' => 'domotic.capabilities.battery', 'reference' => CapabilityReferenceList::BATTERY->value],
        ['label' => 'domotic.capabilities.button', 'reference' => CapabilityReferenceList::BUTTON->value],
        ['label' => 'domotic.capabilities.scene', 'reference' => CapabilityReferenceList::SCENE->value],
        ['label' => 'domotic.capabilities.notify', 'reference' => CapabilityReferenceList::NOTIFY->value],
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
