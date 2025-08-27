<?php

namespace App\Domotic\Infrastructure\Foundry\Factory;

use App\Domotic\Domain\Model\CapabilityState;
use App\Domotic\Domain\ReferenceList\CapabilityStateReferenceList;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityStateFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        [
            'label' => 'domotic.capabilit_states.on_off',
            'reference' => CapabilityStateReferenceList::ON_OFF->value,
            'schema' => [
                'type' => 'boolean',
                'value' => ['on' => true, 'off' => false]
            ]
        ],
        [
            'label' => 'domotic.capabilit_states.brightness',
            'reference' => CapabilityStateReferenceList::BRIGHTNESS->value,
            'schema' => [
                'type' => 'integer',
                'value' => ['min' => 0, 'max' => 100]
            ]
        ],
        [
            'label' => 'domotic.capabilit_states.color',
            'reference' => CapabilityStateReferenceList::COLOR->value,
            'schema' => [
                'type' => 'object',
                'value' => [
                    'r' => ['min' => 0, 'max' => 255],
                    'g' => ['min' => 0, 'max' => 255],
                    'b' => ['min' => 0, 'max' => 255],
                ]
            ]
        ],
        [
            'label' => 'domotic.capabilit_states.color_temp',
            'reference' => CapabilityStateReferenceList::COLOR_TEMP->value,
            'schema' => [
                'type' => 'integer',
                'value' => ['min' => 150, 'max' => 500],
                'unit' => 'mired'
            ]
        ],
        [
            'label' => 'domotic.capabilit_states.position',
            'reference' => CapabilityStateReferenceList::POSITION->value,
            'schema' => [
                'type' => 'integer',
                'value' => ['min' => 0, 'max' => 100],
                'unit' => 'percent'
            ]
        ],
        [
            'label' => 'domotic.capabilit_states.speed',
            'reference' => CapabilityStateReferenceList::SPEED->value,
            'schema' => [
                'type' => 'string',
                'value' => ['low', 'medium', 'high', 'auto'],
            ]
        ],
        [
            'label' => 'domotic.capabilit_states.temperature',
            'reference' => CapabilityStateReferenceList::TEMPERATURE->value,
            'schema' => [
                'type' => 'float',
                'unit' => ['celcius', 'fahrenheit'],
            ]
        ],
        [
            'label' => 'domotic.capabilit_states.temperature_mode',
            'reference' => CapabilityStateReferenceList::TEMPERATURE_MODE->value,
            'schema' => [
                'type' => 'string',
                'value' => ['off', 'heat', 'cool', 'auto', 'eco']
            ]
        ],
        [
            'label' => 'domotic.capabilit_states.locked',
            'reference' => CapabilityStateReferenceList::LOCKED->value,
            'schema' => [
                'type' => 'bool',
                'value' => ['lock' => true, 'unlock' => false]
            ]
        ],
        [
            'label' => 'domotic.capabilit_states.presence',
            'reference' => CapabilityStateReferenceList::PRESENCE->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.contact',
            'reference' => CapabilityStateReferenceList::CONTACT->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.vibration',
            'reference' => CapabilityStateReferenceList::VIBRATION->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.humidity',
            'reference' => CapabilityStateReferenceList::HUMIDITY->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.illuminance',
            'reference' => CapabilityStateReferenceList::ILLUMINANCE->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.air_quality',
            'reference' => CapabilityStateReferenceList::AIR_QUALITY->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.smoke',
            'reference' => CapabilityStateReferenceList::SMOKE->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.water_leak',
            'reference' => CapabilityStateReferenceList::WATER_LEAK->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.sound',
            'reference' => CapabilityStateReferenceList::SOUND->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.power',
            'reference' => CapabilityStateReferenceList::POWER->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.energy',
            'reference' => CapabilityStateReferenceList::ENERGY->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.voltage',
            'reference' => CapabilityStateReferenceList::VOLTAGE->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.current',
            'reference' => CapabilityStateReferenceList::CURRENT->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.battery',
            'reference' => CapabilityStateReferenceList::BATTERY->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.last_event',
            'reference' => CapabilityStateReferenceList::LAST_EVENT->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.',
            'reference' => CapabilityStateReferenceList::SCENE->value,
            'schema' => []
        ],
        [
            'label' => 'domotic.capabilit_states.',
            'reference' => CapabilityStateReferenceList::NOTIFY->value,
            'schema' => []
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
        return CapabilityState::class;
    }
}
