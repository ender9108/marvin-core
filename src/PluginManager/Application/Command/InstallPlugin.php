<?php

namespace Marvin\PluginManager\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;

final readonly class InstallPlugin implements CommandInterface
{
    public function __construct(
        public string $pluginClass,
        public array $secrets = [],
        public array $config = [],
    ) {}
}
