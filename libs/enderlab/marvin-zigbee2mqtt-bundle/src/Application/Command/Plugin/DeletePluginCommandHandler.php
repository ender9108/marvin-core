<?php

namespace EnderLab\Zigbee2mqttBundle\Application\Command\Plugin;

use App\System\Domain\Event\Plugin\PluginDeleted;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEventHandler;

#[AsDomainEventHandler(['$.system.plugin.deleted',])]
class DeletePluginCommandHandler
{
    public function __invoke(PluginDeleted $event): void
    {
        /** @todo a faire quand j'aurais la liste des services à arrêter */
    }
}
