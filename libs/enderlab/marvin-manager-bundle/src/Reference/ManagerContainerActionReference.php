<?php

declare(strict_types=1);

namespace EnderLab\MarvinManagerBundle\Reference;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum ManagerContainerActionReference: string
{
    use EnumToArrayTrait;
    case ACTION_START = 'start';
    case ACTION_RESTART = 'restart';
    case ACTION_RESTART_ALL = 'restart_all';
    case ACTION_STOP = 'stop';
    case ACTION_BUILD = 'build';
    case ACTION_EXEC_CMD = 'exec_cmd';
}
