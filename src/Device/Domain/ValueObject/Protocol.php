<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum Protocol: string
{
    use EnumToArrayTrait;

    case ZIGBEE = 'zigbee';
    case MQTT = 'mqtt';
    case NETWORK = 'network';

    public function toString(): string
    {
        return $this->value;
    }

    public function isZigbee(): bool
    {
        return $this === self::ZIGBEE;
    }

    public function isMqtt(): bool
    {
        return $this === self::MQTT;
    }

    public function isNetwork(): bool
    {
        return $this === self::NETWORK;
    }
}

