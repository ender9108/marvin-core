<?php

namespace Marvin\System\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\System\Domain\ValueObject\Identity\PluginId;

final readonly class DeletePlugin implements SyncCommandInterface
{
    public function __construct(
        public PluginId $id,
    ) {
    }
}
