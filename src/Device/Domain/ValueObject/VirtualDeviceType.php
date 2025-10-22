<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum VirtualDeviceType: string
{
    use EnumToArrayTrait;

    // Time & Date
    case TIME_TRIGGER = 'time_trigger';
    case SUN_TRIGGER = 'sun_trigger';
    case TIMER = 'timer';
    case COUNTER = 'counter';

    // Weather
    case WEATHER = 'weather';
    case WEATHER_ALERT = 'weather_alert';

    // Network & System
    case HTTP_TRIGGER = 'http_trigger';
    case MQTT_VIRTUAL = 'mqtt_virtual';
    case PRESENCE_VIRTUAL = 'presence_virtual';
    case DEVICE_TRACKER = 'device_tracker';

    // Variables & Storage
    case VARIABLE = 'variable';
    case STORAGE = 'storage';

    // Logic
    case CONDITION = 'condition';
    case SCENE = 'scene';
    case SCRIPT = 'script';

    // Notifications
    case NOTIFIER = 'notifier';
    case TTS = 'tts';

    // External Integrations
    case CALENDAR = 'calendar';
    case RSS_FEED = 'rss_feed';
}
