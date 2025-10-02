<?php

namespace Marvin\System\Domain\List;

enum PluginStatusReference: string
{
    case STATUS_ENABLED = 'enabled';
    case STATUS_DISABLED = 'disabled';
}
