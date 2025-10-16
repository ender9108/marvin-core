<?php

namespace Marvin\Domotic\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Domotic\Domain\List\CapabilityActionReference;
use Marvin\Domotic\Domain\List\CapabilityReference;
use Marvin\Domotic\Domain\List\CapabilityStateReference;
use Marvin\Domotic\Domain\Model\CapabilityComposition;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityCompositionFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        [
            'capability' => CapabilityReference::SWITCHABLE->value,
            'capabilityActions' => [CapabilityActionReference::TURN_ON->value],
            'capabilityStates' => [CapabilityStateReference::ON_OFF->value],
        ],
        [
            'capability' => CapabilityReference::DIMMABLE->value,
            'capabilityActions' => [CapabilityActionReference::SET_LEVEL->value],
            'capabilityStates' => [CapabilityStateReference::BRIGHTNESS->value],
        ],
        [
            'capability' => CapabilityReference::COLORABLE->value,
            'capabilityActions' => [CapabilityActionReference::SET_COLOR->value],
            'capabilityStates' => [CapabilityStateReference::COLOR->value, ],
        ],
        [
            'capability' => CapabilityReference::COLORABLE->value,
            'capabilityActions' => [CapabilityActionReference::SET_COLOR->value],
            'capabilityStates' => [
                CapabilityStateReference::COLOR->value,
                CapabilityStateReference::COLOR_TEMP->value
            ],
        ],
        [
            'capability' => CapabilityReference::COVERABLE->value,
            'capabilityActions' => [CapabilityActionReference::SET_POSITION->value],
            'capabilityStates' => [CapabilityStateReference::POSITION->value, ],
        ],
        [
            'capability' => CapabilityReference::FAN->value,
            'capabilityActions' => [
                CapabilityActionReference::FAN_ON->value,
                CapabilityActionReference::FAN_OFF->value,
                CapabilityActionReference::SET_SPEED->value,
            ],
            'capabilityStates' => [
                CapabilityStateReference::ON_OFF->value,
                CapabilityStateReference::SPEED->value,
            ],
        ],
        [
            'capability' => CapabilityReference::THERMOSTAT->value,
            'capabilityActions' => [
                CapabilityActionReference::SET_MODE->value,
                CapabilityActionReference::SET_TARGET->value
            ],
            'capabilityStates' => [
                CapabilityStateReference::TEMPERATURE->value,
                CapabilityStateReference::TEMPERATURE_MODE->value,
            ],
        ],
        [
            'capability' => CapabilityReference::LOCKABLE->value,
            'capabilityActions' => [
                CapabilityActionReference::LOCK->value,
                CapabilityActionReference::UNLOCK->value,
            ],
            'capabilityStates' => [CapabilityStateReference::LOCKED->value, ],
        ],
        [
            'capability' => CapabilityReference::PRESENCE->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::PRESENCE->value, ],
        ],
        [
            'capability' => CapabilityReference::CONTACT->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::CONTACT->value, ],
        ],

        [
            'capability' => CapabilityReference::VIBRATION->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::VIBRATION->value, ],
        ],
        [
            'capability' => CapabilityReference::TEMPERATURE->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::TEMPERATURE->value, ],
        ],
        [
            'capability' => CapabilityReference::HUMIDITY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::HUMIDITY->value, ],
        ],
        [
            'capability' => CapabilityReference::CONTACT->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::CONTACT->value, ],
        ],
        [
            'capability' => CapabilityReference::CONTACT->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::CONTACT->value, ],
        ],
        [
            'capability' => CapabilityReference::HUMIDITY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::HUMIDITY->value, ],
        ],
        [
            'capability' => CapabilityReference::ILLUMINANCE->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::ILLUMINANCE->value, ],
        ],
        [
            'capability' => CapabilityReference::AIR_QUALITY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::AIR_QUALITY->value, ],
        ],
        [
            'capability' => CapabilityReference::SMOKE->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::SMOKE->value, ],
        ],
        [
            'capability' => CapabilityReference::WATER_LEAK->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::WATER_LEAK->value, ],
        ],
        [
            'capability' => CapabilityReference::SOUND->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::SOUND->value, ],
        ],
        [
            'capability' => CapabilityReference::POWER->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::POWER->value, ],
        ],
        [
            'capability' => CapabilityReference::ENERGY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::ENERGY->value, ],
        ],
        [
            'capability' => CapabilityReference::VOLTAGE->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::VOLTAGE->value, ],
        ],
        [
            'capability' => CapabilityReference::CURRENT->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::CURRENT->value, ],
        ],
        [
            'capability' => CapabilityReference::BATTERY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::BATTERY->value, ],
        ],
        [
            'capability' => CapabilityReference::BUTTON->value,
            'capabilityActions' => [
                CapabilityActionReference::BUTTON_PRESS->value,
                CapabilityActionReference::BUTTON_DOUBLE->value,
                CapabilityActionReference::BUTTON_LONG->value,
                CapabilityActionReference::BUTTON_RELEASE->value,
            ],
            'capabilityStates' => [CapabilityStateReference::LAST_EVENT->value, ],
        ],
        [
            'capability' => CapabilityReference::SCENE->value,
            'capabilityActions' => [CapabilityActionReference::ACTIVATE_SCENE->value, ],
            'capabilityStates' => [CapabilityStateReference::SCENE->value, ],
        ],
        [
            'capability' => CapabilityReference::NOTIFY->value,
            'capabilityActions' => [],
            'capabilityStates' => [CapabilityStateReference::NOTIFY->value, ],
        ]
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
