<?php

namespace EnderLab\DddCqrsBundle\Application\Command\Attribute;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsSyncCommandHandler extends AsMessageHandler
{
    public function __construct(int $priority = 0)
    {
        parent::__construct('sync.command.bus', $priority);
    }
}
