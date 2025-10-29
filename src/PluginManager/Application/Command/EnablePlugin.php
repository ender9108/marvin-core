<?php

namespace Marvin\PluginManager\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\PluginId;

final readonly class EnablePlugin implements CommandInterface
{
    public function __construct(
        public PluginId $pluginId,
    ) {}
}
