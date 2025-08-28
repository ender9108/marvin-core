<?php

use App\System\Application\Command\DeletePluginCommand;
use EnderLab\DddCqrsBundle\Application\Command\Attribute\AsCommandHandler;

#[AsCommandHandler]
class DeletePluginCommandHandler
{
    public function __invoke(DeletePluginCommand $command): void
    {
        /** @todo a faire quand j'aurais la liste des services à arrêter */
    }
}
