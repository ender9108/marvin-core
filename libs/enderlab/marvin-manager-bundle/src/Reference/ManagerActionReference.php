<?php
namespace EnderLab\MarvinManagerBundle\Reference;


use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum ManagerActionReference: string
{
    use EnumToArrayTrait;

    case ACTION_START = 'start';
    case ACTION_RESTART = 'restart';
    case ACTION_STOP = 'stop';
    case ACTION_BUILD = 'build';
    case ACTION_EXEC_CMD = 'exec_cmd';
}
