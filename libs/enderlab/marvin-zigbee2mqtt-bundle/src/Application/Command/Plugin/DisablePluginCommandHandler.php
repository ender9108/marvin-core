<?php

namespace EnderLab\Zigbee2mqttBundle\Application\Command\Plugin;

use App\System\Application\Command\Plugin\DisablePluginCommand;
use EnderLab\DddCqrsBundle\Application\Command\Attribute\AsCommandHandler;

#[AsCommandHandler]
class DisablePluginCommandHandler
{
    public function __invoke(DisablePluginCommand $command): void
    {
        /** @todo a faire quand j'aurais la liste des services à arrêter */
    }
}
