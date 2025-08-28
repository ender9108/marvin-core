<?php

namespace EnderLab\Zigbee2mqttBundle\Application\Command\Plugin;

use App\System\Domain\Event\Plugin\PluginEnabled;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEventHandler;

#[AsDomainEventHandler(['$.system.plugin.enabled',])]
class EnablePluginCommandHandler
{
    public function __invoke(PluginEnabled $event): void
    {
        /** @todo a faire quand j'aurais la liste des services à arrêter */
    }
}
