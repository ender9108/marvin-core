<?php

namespace App\Domotic\Domain\ReferenceList;

enum CapabilityStateReferenceList: string
{
    case ON_OFF = 'on_off';
    case BRIGHTNESS = 'brightness';
    case COLOR = 'color';
    case COLOR_TEMP = 'color_temp';
    case POSITION = 'position';
    case SPEED = 'speed';
    case TEMPERATURE = 'temperature';
    case TEMPERATURE_MODE = 'temperature_mode';
    case LOCKED = 'locked';
    case PRESENCE = 'presence';
    case CONTACT = 'contact';
    case VIBRATION = 'vibration';
    case HUMIDITY = 'humidity';
    case ILLUMINANCE = 'illuminance';
    case AIR_QUALITY = 'air_quality';
    case PM2_5 = 'pm2.5';
    case PM10 = 'pm10';
    case VOC_INDEX = 'voc_index';
    case CO2 = 'co2';
    case SMOKE = 'smoke';
    case WATER_LEAK = 'water_leak';
    case SOUND = 'sound';
    case POWER = 'power';
    case ENERGY = 'energy';
    case VOLTAGE = 'voltage';
    case CURRENT = 'current';
    case BATTERY = 'battery';

    // Other
    case LAST_EVENT = 'last_event';
    case SCENE = 'scene';    // defined scene
    case NOTIFY = 'notify';   // fault, alarm
}
