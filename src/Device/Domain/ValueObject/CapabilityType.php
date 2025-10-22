<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum CapabilityType: string
{
    use EnumToArrayTrait;

    // Lighting
    case LIGHT = 'light';
    case DIMMABLE_LIGHT = 'dimmable_light';
    case COLOR_LIGHT = 'color_light';
    case COLOR_TEMPERATURE_LIGHT = 'color_temperature_light';

    // Actuators
    case SWITCH = 'switch';
    case RELAY = 'relay';
    case OUTLET = 'outlet';
    case VALVE = 'valve';
    case LOCK = 'lock';

    // Climate Control
    case HVAC = 'hvac';
    case THERMOSTAT = 'thermostat';
    case FAN = 'fan';
    case HUMIDIFIER = 'humidifier';
    case DEHUMIDIFIER = 'dehumidifier';

    // Covers
    case COVER = 'cover';
    case BLIND = 'blind';
    case CURTAIN = 'curtain';
    case GARAGE_DOOR = 'garage_door';
    case WINDOW = 'window';

    // Environmental Sensors
    case TEMPERATURE_SENSOR = 'temperature_sensor';
    case HUMIDITY_SENSOR = 'humidity_sensor';
    case PRESSURE_SENSOR = 'pressure_sensor';
    case AIR_QUALITY_SENSOR = 'air_quality_sensor';
    case ILLUMINANCE_SENSOR = 'illuminance_sensor';
    case UV_SENSOR = 'uv_sensor';

    // Security Sensors
    case MOTION_SENSOR = 'motion_sensor';
    case OCCUPANCY_SENSOR = 'occupancy_sensor';
    case DOOR_SENSOR = 'door_sensor';
    case WINDOW_SENSOR = 'window_sensor';
    case VIBRATION_SENSOR = 'vibration_sensor';
    case SMOKE_DETECTOR = 'smoke_detector';
    case GAS_DETECTOR = 'gas_detector';
    case WATER_LEAK_DETECTOR = 'water_leak_detector';
    case GLASS_BREAK_SENSOR = 'glass_break_sensor';

    // Controls
    case BUTTON = 'button';
    case DIMMER_SWITCH = 'dimmer_switch';
    case SCENE_SWITCH = 'scene_switch';
    case REMOTE_CONTROL = 'remote_control';

    // Multimedia
    case MEDIA_PLAYER = 'media_player';
    case TV = 'tv';
    case SPEAKER = 'speaker';
    case CAMERA = 'camera';
    case DOORBELL = 'doorbell';

    // Energy
    case POWER_METER = 'power_meter';
    case ENERGY_METER = 'energy_meter';
    case BATTERY = 'battery';
    case SOLAR_PANEL = 'solar_panel';

    // Virtual - Time
    case TIME_TRIGGER = 'time_trigger';
    case SUN_TRIGGER = 'sun_trigger';
    case TIMER = 'timer';
    case COUNTER = 'counter';

    // Virtual - Weather
    case WEATHER = 'weather';
    case WEATHER_ALERT = 'weather_alert';

    // Virtual - Network
    case HTTP_TRIGGER = 'http_trigger';
    case MQTT_VIRTUAL = 'mqtt_virtual';
    case PRESENCE_VIRTUAL = 'presence_virtual';
    case DEVICE_TRACKER = 'device_tracker';

    // Virtual - Variables
    case VARIABLE = 'variable';
    case STORAGE = 'storage';

    // Virtual - Logic
    case CONDITION = 'condition';
    case SCENE = 'scene';
    case SCRIPT = 'script';

    // Virtual - Notifications
    case NOTIFIER = 'notifier';
    case TTS = 'tts';

    // Virtual - Integrations
    case CALENDAR = 'calendar';
    case RSS_FEED = 'rss_feed';
}
