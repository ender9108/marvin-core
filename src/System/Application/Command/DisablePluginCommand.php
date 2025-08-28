<?php

namespace App\System\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
class DisablePluginCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $reference,
    ) {
    }
}
