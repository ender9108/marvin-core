<?php

namespace App\Domotic\Domain\ReferenceList;

use EnderLab\ToolsBundle\Service\ListTrait;

class CapabilityReferenceList
{
    use ListTrait;

    // Basic controls
    public const string SWITCHABLE = 'switchable';
    public const string DIMMABLE = 'dimmable';
    public const string COLORABLE = 'colorable';
    public const string COVERABLE = 'coverable';
    public const string FAN = 'fan';
    public const string THERMOSTAT = 'thermostat';
    public const string LOCKABLE = 'lockable';

    // Sensors
    public const string PRESENCE = 'presence'; // PIR / radar
    public const string CONTACT = 'contact'; // open / close
    public const string VIBRATION = 'vibration';
    public const string TEMPERATURE = 'temperature';
    public const string HUMIDITY = 'humidity';
    public const string ILLUMINANCE = 'illuminance';
    public const string AIR_QUALITY = 'air_quality';
    public const string SMOKE = 'smoke';
    public const string WATER_LEAK = 'water_leak';
    public const string SOUND = 'sound';

    // Energy / power
    public const string POWER = 'power';
    public const string ENERGY = 'energy';
    public const string VOLTAGE = 'voltage';
    public const string CURRENT = 'current';
    public const string BATTERY = 'battery';

    // Other
    public const string BUTTON = 'button';   // click, double click, long press
    public const string SCENE = 'scene';    // defined scene
    public const string NOTIFY = 'notify';   // fault, alarm
}
