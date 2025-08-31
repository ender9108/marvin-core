<?php

namespace App\System\Application\Command\Docker;


use EnderLab\MarvinManagerBundle\System\Infrastructure\List\ManagerMessageReference;
use EnderLab\MarvinManagerBundle\System\Infrastructure\Symfony\Messenger\Attribute\AsMessageType;
use EnderLab\MarvinManagerBundle\System\Infrastructure\Symfony\Messenger\ManagerResponseCommand;

#[AsMessageType(binding: ManagerMessageReference::RESPONSE_INSTALL_DOCKER->value)]
class InstallDockerResponseCommand extends ManagerResponseCommand
{

}
