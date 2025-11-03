<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum Availability: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case UNKNOWN = 'unknown';

    public function toString(): string
    {
        return $this->value;
    }

    public function isOnline(): bool
    {
        return $this === self::ONLINE;
    }

    public function isOffline(): bool
    {
        return $this === self::OFFLINE;
    }
}

