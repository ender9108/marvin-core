<?php

namespace EnderLab\Zigbee2mqttBundle\Application\Command\Plugin;

use App\System\Application\Command\Plugin\EnablePluginCommand;
use EnderLab\DddCqrsBundle\Application\Command\Attribute\AsCommandHandler;

#[AsCommandHandler]
class EnablePluginCommandHandler
{
    public function __invoke(EnablePluginCommand $command): void
    {
        /** @todo a faire quand j'aurais la liste des services à arrêter */
    }
}
