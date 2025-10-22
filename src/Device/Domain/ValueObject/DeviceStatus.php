<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum DeviceStatus: string
{
    use EnumToArrayTrait;

    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case UNAVAILABLE = 'unavailable';
}
