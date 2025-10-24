<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum DeviceType: string
{
    use EnumToArrayTrait;

    case PHYSICAL = 'physical';
    case VIRTUAL = 'virtual';
    case COMPOSITE = 'composite';
}
