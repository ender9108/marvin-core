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

namespace Marvin\Protocol\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum TransportType: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    case MQTT = 'mqtt';
    case REST = 'rest';
    case JSONRPC = 'jsonrpc';
    case WEBSOCKET = 'websocket';

    public function isMqtt(): bool
    {
        return $this === self::MQTT;
    }

    public function isRest(): bool
    {
        return $this === self::REST;
    }

    public function isJsonRpc(): bool
    {
        return $this === self::JSONRPC;
    }

    public function isWebSocket(): bool
    {
        return $this === self::WEBSOCKET;
    }

    public function supportsCorrelation(): bool
    {
        return match ($this) {
            self::MQTT, self::JSONRPC, self::WEBSOCKET => true,
            self::REST => false,
        };
    }
}
