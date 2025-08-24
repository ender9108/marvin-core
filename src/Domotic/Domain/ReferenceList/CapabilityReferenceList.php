<?php

namespace App\Domotic\Domain\ReferenceList;

use EnderLab\ToolsBundle\Service\ListTrait;

enum CapabilityReferenceList: string
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
