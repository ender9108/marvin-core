<?php

namespace App\System\Domain\Event\Plugin;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEvent;

#[AsDomainEvent(routingKey: '$.system.plugin.disabled')]
class PluginDisabled extends AbstractDomainEvent
{
    public function __construct(
        public ?string $id = null,
    ) {
        parent::__construct();
    }
}
