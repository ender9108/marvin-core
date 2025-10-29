<?php

namespace Marvin\PluginManager\Domain\ValueObject;

enum PluginStatus: string
{
    case INSTALLED = 'installed';
    case ENABLED = 'enabled';
    case DISABLED = 'disabled';
    case UPDATE_BLOCKED = 'update_blocked';
}
