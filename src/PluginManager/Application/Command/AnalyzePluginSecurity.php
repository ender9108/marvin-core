<?php

namespace Marvin\PluginManager\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;

final readonly class AnalyzePluginSecurity implements CommandInterface
{
    public function __construct(
        public string $pluginClass,
    ) {
    }
}
