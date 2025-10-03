<?php

namespace Marvin\Domotic\Domain\List;

enum CapabilityReference: string
{
    // Basic controls
    case SWITCHABLE = 'switchable';
    case DIMMABLE = 'dimmable';
    case COLORABLE = 'colorable';
    case COVERABLE = 'coverable';
    case FAN = 'fan';
    case THERMOSTAT = 'thermostat';
    case LOCKABLE = 'lockable';

    // Sensors
    case PRESENCE = 'presence'; // PIR / radar
    case CONTACT = 'contact'; // open / close
    case VIBRATION = 'vibration';
    case TEMPERATURE = 'temperature';
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

    // Energy / power
    case POWER = 'power';
    case ENERGY = 'energy';
    case VOLTAGE = 'voltage';
    case CURRENT = 'current';
    case BATTERY = 'battery';

    // Other
    case BUTTON = 'button';   // click, double click, long press
    case SCENE = 'scene';    // defined scene
    case NOTIFY = 'notify';   // fault, alarm
}
