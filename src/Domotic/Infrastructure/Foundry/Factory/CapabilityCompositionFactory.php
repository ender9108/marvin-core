<?php

namespace App\Domotic\Infrastructure\Foundry\Factory;

use App\Domotic\Domain\Model\CapabilityComposition;
use App\Domotic\Domain\ReferenceList\CapabilityActionReferenceList;
use App\Domotic\Domain\ReferenceList\CapabilityReferenceList;
use App\Domotic\Domain\ReferenceList\CapabilityStateReferenceList;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityCompositionFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        [
            'capability' => CapabilityReferenceList::SWITCHABLE->value,
            'capabilityActions' => [CapabilityActionReferenceList::TURN_ON->value],
            'capabilityStates' => [CapabilityStateReferenceList::ON_OFF->value],
        ],
        [
            'capability' => CapabilityReferenceList::DIMMABLE->value,
            'capabilityActions' => [CapabilityActionReferenceList::SET_LEVEL->value],
            'capabilityStates' => [CapabilityStateReferenceList::BRIGHTNESS->value],
        ],
        [
            'capability' => CapabilityReferenceList::COLORABLE->value,
            'capabilityActions' => [CapabilityActionReferenceList::SET_COLOR->value],
            'capabilityStates' => [CapabilityStateReferenceList::COLOR->value,],
        ],
        [
            'capability' => CapabilityReferenceList::COLORABLE->value,
            'capabilityActions' => [CapabilityActionReferenceList::SET_COLOR->value],
            'capabilityStates' => [
                CapabilityStateReferenceList::COLOR->value,
                CapabilityStateReferenceList::COLOR_TEMP->value
            ],
        ],
        [
            'capability' => CapabilityReferenceList::COVERABLE->value,
            'capabilityActions' => [CapabilityActionReferenceList::SET_POSITION->value],
            'capabilityStates' => [CapabilityStateReferenceList::POSITION->value,],
        ],
        [
            'capability' => CapabilityReferenceList::FAN->value,
            'capabilityActions' => [
                CapabilityActionReferenceList::FAN_ON->value,
                CapabilityActionReferenceList::FAN_OFF->value,
                CapabilityActionReferenceList::SET_SPEED->value,
            ],
            'capabilityStates' => [
                CapabilityStateReferenceList::ON_OFF->value,
                CapabilityStateReferenceList::SPEED->value,
            ],
        ],
        [
            'capability' => CapabilityReferenceList::THERMOSTAT->value,
            'capabilityActions' => [
                CapabilityActionReferenceList::SET_MODE->value,
                CapabilityActionReferenceList::SET_TARGET->value
            ],
            'capabilityStates' => [
                CapabilityStateReferenceList::TEMPERATURE->value,
                CapabilityStateReferenceList::TEMPERATURE_MODE->value,
            ],
        ],
        [
            'capability' => CapabilityReferenceList::LOCKABLE->value,
            'capabilityActions' => [
                CapabilityActionReferenceList::LOCK->value,
                CapabilityActionReferenceList::UNLOCK->value,
            ],
            'capabilityStates' => [CapabilityStateReferenceList::LOCKED->value,],
        ],
        [
            'capability' => CapabilityReferenceList::PRESENCE->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::PRESENCE->value,],
        ],
        [
            'capability' => CapabilityReferenceList::CONTACT->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::CONTACT->value,],
        ],

        [
            'capability' => CapabilityReferenceList::VIBRATION->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::VIBRATION->value,],
        ],
        [
            'capability' => CapabilityReferenceList::TEMPERATURE->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::TEMPERATURE->value,],
        ],
        [
            'capability' => CapabilityReferenceList::HUMIDITY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::HUMIDITY->value,],
        ],
        [
            'capability' => CapabilityReferenceList::CONTACT->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::CONTACT->value,],
        ],
        [
            'capability' => CapabilityReferenceList::CONTACT->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::CONTACT->value,],
        ],
        [
            'capability' => CapabilityReferenceList::HUMIDITY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::HUMIDITY->value,],
        ],
        [
            'capability' => CapabilityReferenceList::ILLUMINANCE->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::ILLUMINANCE->value,],
        ],
        [
            'capability' => CapabilityReferenceList::AIR_QUALITY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::AIR_QUALITY->value,],
        ],
        [
            'capability' => CapabilityReferenceList::SMOKE->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::SMOKE->value,],
        ],
        [
            'capability' => CapabilityReferenceList::WATER_LEAK->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::WATER_LEAK->value,],
        ],
        [
            'capability' => CapabilityReferenceList::SOUND->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::SOUND->value,],
        ],
        [
            'capability' => CapabilityReferenceList::POWER->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::POWER->value,],
        ],
        [
            'capability' => CapabilityReferenceList::ENERGY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::ENERGY->value,],
        ],
        [
            'capability' => CapabilityReferenceList::VOLTAGE->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::VOLTAGE->value,],
        ],
        [
            'capability' => CapabilityReferenceList::CURRENT->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::CURRENT->value,],
        ],
        [
            'capability' => CapabilityReferenceList::BATTERY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::BATTERY->value,],
        ],
        [
            'capability' => CapabilityReferenceList::BUTTON->value,
            'capabilityActions' => [
                CapabilityActionReferenceList::BUTTON_PRESS->value,
                CapabilityActionReferenceList::BUTTON_DOUBLE->value,
                CapabilityActionReferenceList::BUTTON_LONG->value,
                CapabilityActionReferenceList::BUTTON_RELEASE->value,
            ],
            'capabilityStates' => [CapabilityStateReferenceList::LAST_EVENT->value,],
        ],
        [
            'capability' => CapabilityReferenceList::SCENE->value,
            'capabilityActions' => [CapabilityActionReferenceList::ACTIVATE_SCENE->value,],
            'capabilityStates' => [CapabilityStateReferenceList::SCENE->value,],
        ],
        [
            'capability' => CapabilityReferenceList::NOTIFY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReferenceList::NOTIFY->value,],
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
        return CapabilityComposition::class;
    }
}
