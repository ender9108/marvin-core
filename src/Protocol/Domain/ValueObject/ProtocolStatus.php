<?php

namespace Marvin\Protocol\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum ProtocolStatus: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    case CONNECTED = 'connected';
    case DISCONNECTED = 'disconnected';
    case ERROR = 'error';

    public function isConnected(): bool
    {
        return $this === self::CONNECTED;
    }

    public function isDisconnected(): bool
    {
        return $this === self::DISCONNECTED;
    }

    public function isError(): bool
    {
        return $this === self::ERROR;
    }
}
