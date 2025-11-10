<?php

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum UserType: string
{
    use ValueObjectTrait;
    use EnumToArrayTrait;

    case APP = 'app';
    case CLI = 'cli';
}
