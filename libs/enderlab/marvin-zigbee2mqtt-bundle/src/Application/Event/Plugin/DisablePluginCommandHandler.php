<?php

namespace EnderLab\Zigbee2mqttBundle\Application\Command\Plugin;

use App\System\Domain\Event\Plugin\PluginDisabled;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerTrait;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

class DisablePluginCommandHandler implements DomainEventHandlerInterface
{
    use DomainEventHandlerTrait;

    protected static array $routingKeys = ['$.system.plugin.disabled'];

    public function __invoke(DomainEventInterface $event): void
    {
        if (!$event instanceof PluginDisabled) {
            return;
        }

        /** @todo a faire quand j'aurais la liste des services à arrêter */
    }
}
