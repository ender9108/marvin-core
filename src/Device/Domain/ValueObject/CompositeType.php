<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum CompositeType: string
{
    use EnumToArrayTrait;

    case GROUP = 'group';
    case SCENE = 'scene';
}
