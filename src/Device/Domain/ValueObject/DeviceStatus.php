<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum DeviceStatus: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case MAINTENANCE = 'maintenance';
    case ERROR = 'error';

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
}
