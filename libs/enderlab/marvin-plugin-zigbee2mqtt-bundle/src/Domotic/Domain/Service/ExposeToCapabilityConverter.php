<?php

namespace EnderLab\Zigbee2mqttBundle\Domotic\Domain\Service;

use EnderLab\Zigbee2mqttBundle\Domotic\Domain\Service\Dto\CapabilityCompositionDescriptor;
use Marvin\Domotic\Domain\List\CapabilityActionReference;
use Marvin\Domotic\Domain\List\CapabilityReference;
use Marvin\Domotic\Domain\List\CapabilityStateReference;

class ExposeToCapabilityConverter
{
    public function convert(array $exposes): array
    {
        $byCap = [];

        foreach ($exposes as $expose) {
            $features = $this->extractAllFeatures($expose);
            foreach ($features as $feature) {
                foreach ($this->mapFeatureToCapabilities($feature) as $capRef) {
                    $key = $capRef->value;
                    if (!isset($byCap[$key])) {
                        [$defaultActions, $defaultStates] = $this->defaultsForCapability($capRef);
                        $byCap[$key] = [
                            'cap' => $capRef,
                            'actions' => $defaultActions,
                            'states' => $defaultStates,
                        ];
                    } else {
                        // Merge to ensure union
                        [$defaultActions, $defaultStates] = $this->defaultsForCapability($capRef);
                        $byCap[$key]['actions'] = $this->mergeUniqueActions($byCap[$key]['actions'], $defaultActions);
                        $byCap[$key]['states'] = $this->mergeUniqueStates($byCap[$key]['states'], $defaultStates);
                    }
                }
            }
        }

        // Build DTOs
        $out = [];
        foreach ($byCap as $row) {
            $out[] = new CapabilityCompositionDescriptor($row['cap'], $row['actions'], $row['states']);
        }

        return $out;
    }

    private function extractAllFeatures(array $expose): array
    {
        $out = [];

        // The expose itself may be a feature
        $out[] = $expose;

        // Many exposes have nested "features" arrays (composites like light, climate, etc.)
        if (isset($expose['features']) && is_array($expose['features'])) {
            foreach ($expose['features'] as $sub) {
                if (is_array($sub)) {
                    $out = array_merge($out, $this->extractAllFeatures($sub));
                }
            }
        }

        return $out;
    }

    private function mapFeatureToCapabilities(array $feature): array
    {
        $capabilities = [];

        $type = strtolower((string)($feature['type'] ?? ''));
        $name = strtolower((string)($feature['name'] ?? ''));
        $property = strtolower((string)($feature['property'] ?? ''));

        // Helpers to check presence of possible values
        $values = $feature['values'] ?? [];
        if (!is_array($values)) {
            $values = [];
        }

        // Switchable
        if (
            $type === 'binary' && (
                $name === 'state' ||
                $property === 'state' ||
                in_array('ON', $values, true) ||
                in_array('OFF', $values, true)
            )
        ) {
            $capabilities[] = CapabilityReference::SWITCHABLE;
        }

        // Dimmable
        if ($type === 'numeric' && ($name === 'brightness' || $property === 'brightness')) {
            $capabilities[] = CapabilityReference::DIMMABLE;
        }

        // Colorable (hue/sat, color temp or xy)
        if (
            ($type === 'composite' && ($name === 'color' || $property === 'color'))
            || ($type === 'numeric' && ($name === 'color_temp' || $property === 'color_temp'))
            || ($type === 'numeric' && ($name === 'hue' || $property === 'hue'))
            || ($type === 'numeric' && ($name === 'saturation' || $property === 'saturation'))
            || ($type === 'numeric' && ($name === 'x' || $property === 'x'))
            || ($type === 'numeric' && ($name === 'y' || $property === 'y'))
        ) {
            $capabilities[] = CapabilityReference::COLORABLE;
        }

        // Coverable (window covering / position, tilt)
        if (
            ($type === 'binary' && ($name === 'state' || $property === 'state') && ($feature['endpoint'] ?? '') === 'cover')
            || ($type === 'numeric' && ($name === 'position' || $property === 'position'))
            || ($type === 'numeric' && ($name === 'tilt' || $property === 'tilt'))
        ) {
            $capabilities[] = CapabilityReference::COVERABLE;
        }

        // Fan
        if ($type === 'enum' && ($name === 'mode' || $property === 'mode') && $this->hasAny($values, ['off','low','medium','high','auto'])) {
            $capabilities[] = CapabilityReference::FAN;
        }

        // Thermostat
        if (
            ($type === 'composite' && ($name === 'climate' || $property === 'climate'))
            || ($type === 'numeric' && ($name === 'temperature_setpoint' || $property === 'temperature_setpoint'))
            || ($type === 'enum' && ($name === 'system_mode' || $property === 'system_mode'))
        ) {
            $capabilities[] = CapabilityReference::THERMOSTAT;
        }

        // Lockable
        if ($type === 'binary' && ($name === 'lock' || $property === 'lock' || $property === 'locked')) {
            $capabilities[] = CapabilityReference::LOCKABLE;
        }

        // Sensors
        if ($type === 'binary' && ($name === 'occupancy' || $property === 'occupancy' || $name === 'presence' || $property === 'presence')) {
            $capabilities[] = CapabilityReference::PRESENCE;
        }
        if ($type === 'binary' && ($name === 'contact' || $property === 'contact')) {
            $capabilities[] = CapabilityReference::CONTACT;
        }
        if ($type === 'binary' && ($name === 'vibration' || $property === 'vibration')) {
            $capabilities[] = CapabilityReference::VIBRATION;
        }
        if ($type === 'numeric' && ($name === 'temperature' || $property === 'temperature')) {
            $capabilities[] = CapabilityReference::TEMPERATURE;
        }
        if ($type === 'numeric' && ($name === 'humidity' || $property === 'humidity')) {
            $capabilities[] = CapabilityReference::HUMIDITY;
        }
        if ($type === 'numeric' && ($name === 'illuminance' || $property === 'illuminance' || $name === 'illuminance_lux' || $property === 'illuminance_lux')) {
            $capabilities[] = CapabilityReference::ILLUMINANCE;
        }
        if ($type === 'numeric' && ($name === 'pm25' || $property === 'pm25' || $name === 'pm2.5' || $property === 'pm2.5')) {
            $capabilities[] = CapabilityReference::PM2_5;
        }
        if ($type === 'numeric' && ($name === 'pm10' || $property === 'pm10')) {
            $capabilities[] = CapabilityReference::PM10;
        }
        if ($type === 'numeric' && ($name === 'voc_index' || $property === 'voc_index' || $name === 'voc' || $property === 'voc')) {
            $capabilities[] = CapabilityReference::VOC_INDEX;
        }
        if ($type === 'numeric' && ($name === 'co2' || $property === 'co2')) {
            $capabilities[] = CapabilityReference::CO2;
        }
        if ($type === 'binary' && ($name === 'smoke' || $property === 'smoke')) {
            $capabilities[] = CapabilityReference::SMOKE;
        }
        if ($type === 'binary' && ($name === 'water_leak' || $property === 'water_leak' || $name === 'leak' || $property === 'leak')) {
            $capabilities[] = CapabilityReference::WATER_LEAK;
        }
        if ($type === 'numeric' && ($name === 'noise' || $property === 'noise' || $name === 'sound' || $property === 'sound')) {
            $capabilities[] = CapabilityReference::SOUND;
        }

        // Energy / power
        if ($type === 'numeric' && ($name === 'power' || $property === 'power')) {
            $capabilities[] = CapabilityReference::POWER;
        }
        if ($type === 'numeric' && ($name === 'energy' || $property === 'energy')) {
            $capabilities[] = CapabilityReference::ENERGY;
        }
        if ($type === 'numeric' && ($name === 'voltage' || $property === 'voltage')) {
            $capabilities[] = CapabilityReference::VOLTAGE;
        }
        if ($type === 'numeric' && ($name === 'current' || $property === 'current')) {
            $capabilities[] = CapabilityReference::CURRENT;
        }
        if ($type === 'numeric' && ($name === 'battery' || $property === 'battery')) {
            $capabilities[] = CapabilityReference::BATTERY;
        }

        // Other
        if ($type === 'enum' && ($name === 'action' || $property === 'action')) {
            // Most remotes expose button actions as an enum of actions
            $capabilities[] = CapabilityReference::BUTTON;
        }
        if ($type === 'enum' && ($name === 'scene' || $property === 'scene')) {
            $capabilities[] = CapabilityReference::SCENE;
        }
        if ($type === 'binary' && ($name === 'alarm' || $property === 'alarm' || $name === 'fault' || $property === 'fault')) {
            $capabilities[] = CapabilityReference::NOTIFY;
        }

        return $capabilities;
    }

    /**
     * Return the default actions and states for a given capability.
     *
     * @return array{0: CapabilityActionReference[], 1: CapabilityStateReference[]}
     */
    private function defaultsForCapability(CapabilityReference $capability): array
    {
        return match ($capability) {
            CapabilityReference::SWITCHABLE => [
                [CapabilityActionReference::TURN_ON, CapabilityActionReference::TURN_OFF],
                [CapabilityStateReference::ON_OFF],
            ],
            CapabilityReference::DIMMABLE => [
                [CapabilityActionReference::SET_LEVEL],
                [CapabilityStateReference::BRIGHTNESS],
            ],
            CapabilityReference::COLORABLE => [
                [CapabilityActionReference::SET_COLOR, CapabilityActionReference::SET_COLOR_TEMP],
                [CapabilityStateReference::COLOR, CapabilityStateReference::COLOR_TEMP],
            ],
            CapabilityReference::COVERABLE => [
                [CapabilityActionReference::OPEN, CapabilityActionReference::CLOSE, CapabilityActionReference::STOP, CapabilityActionReference::SET_POSITION],
                [CapabilityStateReference::POSITION],
            ],
            CapabilityReference::FAN => [
                [CapabilityActionReference::FAN_ON, CapabilityActionReference::FAN_OFF, CapabilityActionReference::SET_SPEED],
                [CapabilityStateReference::ON_OFF, CapabilityStateReference::SPEED],
            ],
            CapabilityReference::THERMOSTAT => [
                [CapabilityActionReference::SET_MODE, CapabilityActionReference::SET_TARGET],
                [CapabilityStateReference::TEMPERATURE, CapabilityStateReference::TEMPERATURE_MODE],
            ],
            CapabilityReference::LOCKABLE => [
                [CapabilityActionReference::LOCK, CapabilityActionReference::UNLOCK],
                [CapabilityStateReference::LOCKED],
            ],

            CapabilityReference::PRESENCE => [[], [CapabilityStateReference::PRESENCE]],
            CapabilityReference::CONTACT => [[], [CapabilityStateReference::CONTACT]],
            CapabilityReference::VIBRATION => [[], [CapabilityStateReference::VIBRATION]],
            CapabilityReference::TEMPERATURE => [[], [CapabilityStateReference::TEMPERATURE]],
            CapabilityReference::HUMIDITY => [[], [CapabilityStateReference::HUMIDITY]],
            CapabilityReference::ILLUMINANCE => [[], [CapabilityStateReference::ILLUMINANCE]],
            CapabilityReference::AIR_QUALITY => [[], [CapabilityStateReference::AIR_QUALITY]],
            CapabilityReference::PM2_5 => [[], [CapabilityStateReference::PM2_5]],
            CapabilityReference::PM10 => [[], [CapabilityStateReference::PM10]],
            CapabilityReference::VOC_INDEX => [[], [CapabilityStateReference::VOC_INDEX]],
            CapabilityReference::CO2 => [[], [CapabilityStateReference::CO2]],
            CapabilityReference::SMOKE => [[], [CapabilityStateReference::SMOKE]],
            CapabilityReference::WATER_LEAK => [[], [CapabilityStateReference::WATER_LEAK]],
            CapabilityReference::SOUND => [[], [CapabilityStateReference::SOUND]],

            CapabilityReference::POWER => [[], [CapabilityStateReference::POWER]],
            CapabilityReference::ENERGY => [[], [CapabilityStateReference::ENERGY]],
            CapabilityReference::VOLTAGE => [[], [CapabilityStateReference::VOLTAGE]],
            CapabilityReference::CURRENT => [[], [CapabilityStateReference::CURRENT]],
            CapabilityReference::BATTERY => [[], [CapabilityStateReference::BATTERY]],

            CapabilityReference::BUTTON => [[
                CapabilityActionReference::BUTTON_PRESS,
                CapabilityActionReference::BUTTON_DOUBLE,
                CapabilityActionReference::BUTTON_LONG,
                CapabilityActionReference::BUTTON_RELEASE,
            ], [CapabilityStateReference::LAST_EVENT]],

            CapabilityReference::SCENE => [[CapabilityActionReference::ACTIVATE_SCENE], [CapabilityStateReference::SCENE]],
            CapabilityReference::NOTIFY => [[], [CapabilityStateReference::NOTIFY]],
        };
    }

    private function mergeUniqueActions(array $a, array $b): array
    {
        $out = [];
        foreach (array_merge($a, $b) as $item) {
            $out[$item->value] = $item;
        }
        return array_values($out);
    }

    private function mergeUniqueStates(array $a, array $b): array
    {
        $out = [];
        foreach (array_merge($a, $b) as $item) {
            $out[$item->value] = $item;
        }
        return array_values($out);
    }

    private function hasAny(array $haystack, array $needles): bool
    {
        $haystackLower = array_map(static fn($v) => is_string($v) ? strtolower($v) : $v, $haystack);

        return array_any(
            $needles,
            fn($needle) => in_array(strtolower($needle), $haystackLower, true)
        );
    }
}
