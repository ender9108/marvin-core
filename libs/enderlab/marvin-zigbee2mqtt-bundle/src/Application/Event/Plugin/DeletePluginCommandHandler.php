<?php

namespace EnderLab\Zigbee2mqttBundle\Application\Event\Plugin;

use App\System\Domain\Event\Plugin\PluginDeleted;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerTrait;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

class DeletePluginCommandHandler implements DomainEventHandlerInterface
{
    use DomainEventHandlerTrait;

    protected static array $routingKeys = ['$.system.plugin.deleted'];

    public function __invoke(DomainEventInterface $event): void
    {
        if (!$event instanceof PluginDeleted) {
            return;
        }

        /** @todo a faire quand j'aurais la liste des services à arrêter */
    }
}
