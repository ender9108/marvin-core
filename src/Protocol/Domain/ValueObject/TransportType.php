<?php

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
