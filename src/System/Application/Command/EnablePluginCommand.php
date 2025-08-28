<?php

namespace App\System\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
class EnablePluginCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $reference,
    ) {
    }
}
