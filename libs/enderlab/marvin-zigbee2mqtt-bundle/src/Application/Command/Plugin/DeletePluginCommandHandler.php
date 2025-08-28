<?php

namespace EnderLab\Zigbee2mqttBundle\Application\Command\Plugin;

use App\System\Application\Command\Plugin\DeletePluginCommand;
use EnderLab\DddCqrsBundle\Application\Command\Attribute\AsCommandHandler;

#[AsCommandHandler]
class DeletePluginCommandHandler
{
    public function __invoke(DeletePluginCommand $command): void
    {
        /** @todo a faire quand j'aurais la liste des services à arrêter */
    }
}
