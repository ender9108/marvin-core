<?php

namespace App\Domotic\Infrastructure\DataFixtures\Foundry\Factory;

use Marvin\Domotic\Domain\List\CapabilityStateAirQualityReference;
use Marvin\Domotic\Domain\List\CapabilityStateNotifyReference;
use Marvin\Domotic\Domain\List\CapabilityStateReference;
use Marvin\Domotic\Domain\Model\CapabilityState;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityStateFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        [
            'label' => 'domotic.capability_states.on_off',
            'reference' => CapabilityStateReference::ON_OFF->value,
            'stateSchema' => ['type' => 'bool', ]
        ],
        [
            'label' => 'domotic.capability_states.brightness',
            'reference' => CapabilityStateReference::BRIGHTNESS->value,
            'stateSchema' => [
                'type' => 'integer',
                'value' => ['min' => 0, 'max' => 100]
            ]
        ],
        [
            'label' => 'domotic.capability_states.color',
            'reference' => CapabilityStateReference::COLOR->value,
            'stateSchema' => [
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
            'reference' => CapabilityStateReference::COLOR_TEMP->value,
            'stateSchema' => [
                'type' => 'integer',
                'value' => ['min' => 150, 'max' => 500],
                'unit' => 'mired'
            ]
        ],
        [
            'label' => 'domotic.capability_states.position',
            'reference' => CapabilityStateReference::POSITION->value,
            'stateSchema' => [
                'type' => 'integer',
                'value' => ['min' => 0, 'max' => 100],
                'unit' => 'percent'
            ]
        ],
        [
            'label' => 'domotic.capability_states.speed',
            'reference' => CapabilityStateReference::SPEED->value,
            'stateSchema' => [
                'type' => 'string',
                'value' => ['low', 'medium', 'high', 'auto'],
            ]
        ],
        [
            'label' => 'domotic.capability_states.temperature',
            'reference' => CapabilityStateReference::TEMPERATURE->value,
            'stateSchema' => [
                'type' => 'float',
                'unit' => ['celcius', 'fahrenheit'],
            ]
        ],
        [
            'label' => 'domotic.capability_states.temperature_mode',
            'reference' => CapabilityStateReference::TEMPERATURE_MODE->value,
            'stateSchema' => [
                'type' => 'string',
                'value' => ['off', 'heat', 'cool', 'auto', 'eco']
            ]
        ],
        [
            'label' => 'domotic.capability_states.locked',
            'reference' => CapabilityStateReference::LOCKED->value,
            'stateSchema' => ['type' => 'bool', ]
        ],
        [
            'label' => 'domotic.capability_states.presence',
            'reference' => CapabilityStateReference::PRESENCE->value,
            'stateSchema' => ['type' => 'bool', ]
        ],
        [
            'label' => 'domotic.capability_states.contact',
            'reference' => CapabilityStateReference::CONTACT->value,
            'stateSchema' => ['type' => 'bool', ]
        ],
        [
            'label' => 'domotic.capability_states.vibration',
            'reference' => CapabilityStateReference::VIBRATION->value,
            'stateSchema' => ['type' => 'bool', ]
        ],
        [
            'label' => 'domotic.capability_states.humidity',
            'reference' => CapabilityStateReference::HUMIDITY->value,
            'stateSchema' => [
                'type' => 'float',
                'unit' => 'percent',
            ]
        ],
        [
            'label' => 'domotic.capability_states.illuminance',
            'reference' => CapabilityStateReference::ILLUMINANCE->value,
            'stateSchema' => [
                'type' => 'integer',
                'unit' => 'lux',
            ]
        ],
        [
            'label' => 'domotic.capability_states.air_quality',
            'reference' => CapabilityStateReference::AIR_QUALITY->value,
            'stateSchema' => [
                'type' => 'string',
                'value' => [
                    CapabilityStateAirQualityReference::EXCELLENT->value,
                    CapabilityStateAirQualityReference::GOOD->value,
                    CapabilityStateAirQualityReference::MODERATE->value,
                    CapabilityStateAirQualityReference::POOR->value,
                    CapabilityStateAirQualityReference::UNHEALTHY->value,
                    CapabilityStateAirQualityReference::HAZARDOUS->value,
                ]
            ]
        ],
        [
            'label' => 'domotic.capability_states.pm2_5',
            'reference' => CapabilityStateReference::PM2_5->value,
            'stateSchema' => ['type' => 'float', ]
        ],
        [
            'label' => 'domotic.capability_states.pm10',
            'reference' => CapabilityStateReference::PM10->value,
            'stateSchema' => ['type' => 'float', ]
        ],
        [
            'label' => 'domotic.capability_states.voc_index',
            'reference' => CapabilityStateReference::VOC_INDEX->value,
            'stateSchema' => ['type' => 'float', ]
        ],
        [
            'label' => 'domotic.capability_states.co2',
            'reference' => CapabilityStateReference::CO2->value,
            'stateSchema' => ['type' => 'integer', ]
        ],
        [
            'label' => 'domotic.capability_states.smoke',
            'reference' => CapabilityStateReference::SMOKE->value,
            'stateSchema' => ['type' => 'bool', ]
        ],
        [
            'label' => 'domotic.capability_states.water_leak',
            'reference' => CapabilityStateReference::WATER_LEAK->value,
            'stateSchema' => ['type' => 'bool', ]
        ],
        [
            'label' => 'domotic.capability_states.sound',
            'reference' => CapabilityStateReference::SOUND->value,
            'stateSchema' => [
                'type' => 'integer',
                'unit' => 'dB',
            ]
        ],
        [
            'label' => 'domotic.capability_states.power',
            'reference' => CapabilityStateReference::POWER->value,
            'stateSchema' => [
                'type' => 'float',
                'unit' => 'W',
            ]
        ],
        [
            'label' => 'domotic.capability_states.energy',
            'reference' => CapabilityStateReference::ENERGY->value,
            'stateSchema' => [
                'type' => 'float',
                'unit' => 'kWh',
            ]
        ],
        [
            'label' => 'domotic.capability_states.voltage',
            'reference' => CapabilityStateReference::VOLTAGE->value,
            'stateSchema' => [
                'type' => 'float',
                'unit' => 'V',
            ]
        ],
        [
            'label' => 'domotic.capability_states.current',
            'reference' => CapabilityStateReference::CURRENT->value,
            'stateSchema' => [
                'type' => 'float',
                'unit' => 'A',
            ]
        ],
        [
            'label' => 'domotic.capability_states.battery',
            'reference' => CapabilityStateReference::BATTERY->value,
            'stateSchema' => [
                'type' => 'integer',
                'unit' => 'percent',
            ]
        ],
        [
            'label' => 'domotic.capability_states.last_event',
            'reference' => CapabilityStateReference::LAST_EVENT->value,
            'stateSchema' => [
                'type' => 'string',
                'value' => ['press', 'double', 'long', 'release']
            ]
        ],
        [
            'label' => 'domotic.capability_states.scene',
            'reference' => CapabilityStateReference::SCENE->value,
            'stateSchema' => ['type' => 'string']
        ],
        [
            'label' => 'domotic.capability_states.notify',
            'reference' => CapabilityStateReference::NOTIFY->value,
            'stateSchema' => [
                'type' => 'string',
                'value' => [
                    CapabilityStateNotifyReference::ALL->value,
                    CapabilityStateNotifyReference::BEEP->value,
                    CapabilityStateNotifyReference::BEEP_LONG->value,
                    CapabilityStateNotifyReference::CHIME->value,
                    CapabilityStateNotifyReference::SIREN->value,
                    CapabilityStateNotifyReference::MUTE->value,
                    CapabilityStateNotifyReference::BLINK->value,
                    CapabilityStateNotifyReference::FLASH->value,
                    CapabilityStateNotifyReference::STROBE->value,
                    CapabilityStateNotifyReference::PULSE->value,
                    CapabilityStateNotifyReference::VIBRATE_SHORT->value,
                    CapabilityStateNotifyReference::VIBRATE_LONG->value,
                    CapabilityStateNotifyReference::VIBRATE_PATTERN->value,
                    CapabilityStateNotifyReference::TEXT->value,
                    CapabilityStateNotifyReference::SCROLL_TEXT->value,
                    CapabilityStateNotifyReference::ICON->value,
                    CapabilityStateNotifyReference::SOUND_AND_LIGHT->value,
                    CapabilityStateNotifyReference::SOUND_AND_VIBRATION->value,

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
        return CapabilityState::class;
    }
}
