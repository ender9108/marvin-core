<?php

namespace EnderLab\Zigbee2mqttBundle\Application\Command\Plugin;

use App\System\Domain\Event\Plugin\PluginDisabled;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEventHandler;

#[AsDomainEventHandler(['$.system.plugin.disabled',])]
class DisablePluginCommandHandler
{
    public function __invoke(PluginDisabled $event): void
    {
        /** @todo a faire quand j'aurais la liste des services à arrêter */
    }
}
