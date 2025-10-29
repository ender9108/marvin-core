<?php

namespace Marvin\PluginManager\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\PluginId;

final readonly class DisablePlugin implements CommandInterface
{
    public function __construct(
        public PluginId $pluginId,
        public ?string $reason = null,
    ) {}
}
