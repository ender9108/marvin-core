<?php

namespace Marvin\PluginManager\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\PluginManager\Domain\ValueObject\PluginVersion;
use Marvin\Shared\Domain\ValueObject\Identity\PluginId;

final readonly class UpdatePlugin implements CommandInterface
{
    public function __construct(
        public PluginId $pluginId,
        public ?PluginVersion $targetVersion = null,
        public bool $force = false,
    ) {}
}
