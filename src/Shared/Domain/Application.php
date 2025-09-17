<?php

namespace Marvin\Shared\Domain;

use EnderLab\ToolsBundle\Service\ListTrait;

final class Application
{
    use ListTrait;

    public const string APP_NAME = 'Marvin';
    public const array APP_AVAILABLE_LOCALES = ['fr', 'en'];
    public const array APP_AVAILABLE_THEMES = ['dark', 'light'];
}
