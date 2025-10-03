<?php

namespace App\Domotic\Infrastructure\Foundry\Factory;

use App\Domotic\Domain\Model\CapabilityState;
use App\Domotic\Domain\ReferenceList\CapabilityStateAirQualityType;
use App\Domotic\Domain\ReferenceList\CapabilityStateNotifyType;
use App\Domotic\Domain\ReferenceList\CapabilityStateReferenceList;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityStateFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        [
            'label' => 'domotic.capability_states.on_off',
            'reference' => CapabilityStateReferenceList::ON_OFF->value,
            'schema' => ['type' => 'bool',]
        ],
        [
            'label' => 'domotic.capability_states.brightness',
            'reference' => CapabilityStateReferenceList::BRIGHTNESS->value,
            'schema' => [
                'type' => 'integer',
                'value' => ['min' => 0, 'max' => 100]
            ]
        ],
        [
            'label' => 'domotic.capability_states.color',
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
            'label' => 'domotic.capability_states.color_temp',
            'reference' => CapabilityStateReferenceList::COLOR_TEMP->value,
            'schema' => [
                'type' => 'integer',
                'value' => ['min' => 150, 'max' => 500],
                'unit' => 'mired'
            ]
        ],
        [
            'label' => 'domotic.capability_states.position',
            'reference' => CapabilityStateReferenceList::POSITION->value,
            'schema' => [
                'type' => 'integer',
                'value' => ['min' => 0, 'max' => 100],
                'unit' => 'percent'
            ]
        ],
        [
            'label' => 'domotic.capability_states.speed',
            'reference' => CapabilityStateReferenceList::SPEED->value,
            'schema' => [
                'type' => 'string',
                'value' => ['low', 'medium', 'high', 'auto'],
            ]
        ],
        [
            'label' => 'domotic.capability_states.temperature',
            'reference' => CapabilityStateReferenceList::TEMPERATURE->value,
            'schema' => [
                'type' => 'float',
                'unit' => ['celcius', 'fahrenheit'],
            ]
        ],
        [
            'label' => 'domotic.capability_states.temperature_mode',
            'reference' => CapabilityStateReferenceList::TEMPERATURE_MODE->value,
            'schema' => [
                'type' => 'string',
                'value' => ['off', 'heat', 'cool', 'auto', 'eco']
            ]
        ],
        [
            'label' => 'domotic.capability_states.locked',
            'reference' => CapabilityStateReferenceList::LOCKED->value,
            'schema' => ['type' => 'bool',]
        ],
        [
            'label' => 'domotic.capability_states.presence',
            'reference' => CapabilityStateReferenceList::PRESENCE->value,
            'schema' => ['type' => 'bool',]
        ],
        [
            'label' => 'domotic.capability_states.contact',
            'reference' => CapabilityStateReferenceList::CONTACT->value,
            'schema' => ['type' => 'bool',]
        ],
        [
            'label' => 'domotic.capability_states.vibration',
            'reference' => CapabilityStateReferenceList::VIBRATION->value,
            'schema' => ['type' => 'bool',]
        ],
        [
            'label' => 'domotic.capability_states.humidity',
            'reference' => CapabilityStateReferenceList::HUMIDITY->value,
            'schema' => [
                'type' => 'float',
                'unit' => 'percent',
            ]
        ],
        [
            'label' => 'domotic.capability_states.illuminance',
            'reference' => CapabilityStateReferenceList::ILLUMINANCE->value,
            'schema' => [
                'type' => 'integer',
                'unit' => 'lux',
            ]
        ],
        [
            'label' => 'domotic.capability_states.air_quality',
            'reference' => CapabilityStateReferenceList::AIR_QUALITY->value,
            'schema' => [
                'type' => 'string',
                'value' => [
                    CapabilityStateAirQualityType::EXCELLENT->value,
                    CapabilityStateAirQualityType::GOOD->value,
                    CapabilityStateAirQualityType::MODERATE->value,
                    CapabilityStateAirQualityType::POOR->value,
                    CapabilityStateAirQualityType::UNHEALTHY->value,
                    CapabilityStateAirQualityType::HAZARDOUS->value,
                ]
            ]
        ],
        [
            'label' => 'domotic.capability_states.pm2_5',
            'reference' => CapabilityStateReferenceList::PM2_5->value,
            'schema' => ['type' => 'float',]
        ],
        [
            'label' => 'domotic.capability_states.pm10',
            'reference' => CapabilityStateReferenceList::PM10->value,
            'schema' => ['type' => 'float',]
        ],
        [
            'label' => 'domotic.capability_states.voc_index',
            'reference' => CapabilityStateReferenceList::VOC_INDEX->value,
            'schema' => ['type' => 'float',]
        ],
        [
            'label' => 'domotic.capability_states.co2',
            'reference' => CapabilityStateReferenceList::CO2->value,
            'schema' => ['type' => 'integer',]
        ],
        [
            'label' => 'domotic.capability_states.smoke',
            'reference' => CapabilityStateReferenceList::SMOKE->value,
            'schema' => ['type' => 'bool',]
        ],
        [
            'label' => 'domotic.capability_states.water_leak',
            'reference' => CapabilityStateReferenceList::WATER_LEAK->value,
            'schema' => ['type' => 'bool',]
        ],
        [
            'label' => 'domotic.capability_states.sound',
            'reference' => CapabilityStateReferenceList::SOUND->value,
            'schema' => [
                'type' => 'integer',
                'unit' => 'dB',
            ]
        ],
        [
            'label' => 'domotic.capability_states.power',
            'reference' => CapabilityStateReferenceList::POWER->value,
            'schema' => [
                'type' => 'float',
                'unit' => 'W',
            ]
        ],
        [
            'label' => 'domotic.capability_states.energy',
            'reference' => CapabilityStateReferenceList::ENERGY->value,
            'schema' => [
                'type' => 'float',
                'unit' => 'kWh',
            ]
        ],
        [
            'label' => 'domotic.capability_states.voltage',
            'reference' => CapabilityStateReferenceList::VOLTAGE->value,
            'schema' => [
                'type' => 'float',
                'unit' => 'V',
            ]
        ],
        [
            'label' => 'domotic.capability_states.current',
            'reference' => CapabilityStateReferenceList::CURRENT->value,
            'schema' => [
                'type' => 'float',
                'unit' => 'A',
            ]
        ],
        [
            'label' => 'domotic.capability_states.battery',
            'reference' => CapabilityStateReferenceList::BATTERY->value,
            'schema' => [
                'type' => 'integer',
                'unit' => 'percent',
            ]
        ],
        [
            'label' => 'domotic.capability_states.last_event',
            'reference' => CapabilityStateReferenceList::LAST_EVENT->value,
            'schema' => [
                'type' => 'string',
                'value' => ['press', 'double', 'long', 'release']
            ]
        ],
        [
            'label' => 'domotic.capability_states.scene',
            'reference' => CapabilityStateReferenceList::SCENE->value,
            'schema' => ['type' => 'string']
        ],
        [
            'label' => 'domotic.capability_states.notify',
            'reference' => CapabilityStateReferenceList::NOTIFY->value,
            'schema' => [
                'type' => 'string',
                'value' => [
                    CapabilityStateNotifyType::ALL->value,
                    CapabilityStateNotifyType::BEEP->value,
                    CapabilityStateNotifyType::BEEP_LONG->value,
                    CapabilityStateNotifyType::CHIME->value,
                    CapabilityStateNotifyType::SIREN->value,
                    CapabilityStateNotifyType::MUTE->value,
                    CapabilityStateNotifyType::BLINK->value,
                    CapabilityStateNotifyType::FLASH->value,
                    CapabilityStateNotifyType::STROBE->value,
                    CapabilityStateNotifyType::PULSE->value,
                    CapabilityStateNotifyType::VIBRATE_SHORT->value,
                    CapabilityStateNotifyType::VIBRATE_LONG->value,
                    CapabilityStateNotifyType::VIBRATE_PATTERN->value,
                    CapabilityStateNotifyType::TEXT->value,
                    CapabilityStateNotifyType::SCROLL_TEXT->value,
                    CapabilityStateNotifyType::ICON->value,
                    CapabilityStateNotifyType::SOUND_AND_LIGHT->value,
                    CapabilityStateNotifyType::SOUND_AND_VIBRATION->value,

                ]
            ]
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
        return CapabilityState::class;
    }
}
