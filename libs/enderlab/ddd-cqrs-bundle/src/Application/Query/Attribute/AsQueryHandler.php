<?php

namespace EnderLab\DddCqrsBundle\Application\Query\Attribute;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsQueryHandler extends AsMessageHandler
{
    public function __construct(int $priority = 0)
    {
        parent::__construct('query.bus', $priority);
    }
}
