<?php

declare(strict_types=1);

namespace EnderLab\MarvinManagerBundle\Reference;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum ManagerWorkerActionReference: string
{
    use EnumToArrayTrait;
    case ACTION_START = 'start';
    case ACTION_RESTART = 'restart';
    case ACTION_STOP = 'stop';
    case ACTION_UPDATE = 'update';
    case ACTION_REREAD = 'reread';
}
