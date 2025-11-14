<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Shared\Domain;

use EnderLab\ToolsBundle\Service\ListTrait;

final class Application
{
    use ListTrait;

    public const string APP_NAME = 'Marvin';
    public const array APP_AVAILABLE_LOCALES = ['fr', 'en'];
    public const string APP_DEFAULT_LOCALE = 'fr';
    public const array APP_AVAILABLE_THEMES = ['dark', 'light'];
    public const string APP_DEFAULT_THEME = 'dark';
    public const string APP_DEFAULT_TIMEZONE = 'Europe/Paris';

    public const string APP_EMAIL_FROM = 'app-marvin@marvin.fr';
    public const string APP_EMAIL_NAME = 'Marvin';

    public const string APP_PROTOCOL_TYPE_NETWORK = 'network';
    public const string APP_PROTOCOL_TYPE_ZIGBEE = 'zigbee';
    public const string APP_PROTOCOL_TYPE_MATTER = 'matter';
    public const string APP_PROTOCOL_TYPE_THREAD = 'thread';
    public const string APP_PROTOCOL_TYPE_ZWAVE = 'zwave';

    public const array APP_PROTOCOL_TYPES_AVAILABLES = [
        self::APP_PROTOCOL_TYPE_NETWORK,
        self::APP_PROTOCOL_TYPE_ZIGBEE,
    ];

    public const array APP_WEATHER_PRIVIDER_AVAILABLES = [
        'openweathermap', 'weatherapi', 'meteofrance',
    ];
}
