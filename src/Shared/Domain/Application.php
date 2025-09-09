<?php
namespace Marvin\Shared\Domain;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum Application: string
{
    use EnumToArrayTrait;

    case APP_NAME = 'Marvin';
}
