<?php

namespace EnderLab\Zigbee2mqttBundle\Application\Command\Plugin;

use App\System\Domain\Event\Plugin\PluginEnabled;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerInterface;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerTrait;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEventHandler;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

#[AsDomainEventHandler(['$.system.plugin.enabled',])]
class EnablePluginCommandHandler implements DomainEventHandlerInterface
{
    use DomainEventHandlerTrait;

    protected static array $routingKeys = ['$.system.plugin.enabled'];

    public function __invoke(DomainEventInterface $event): void
    {
        if (!$event instanceof PluginEnabled) {
            return;
        }

        /** @todo a faire quand j'aurais la liste des services à arrêter */
    }
}
