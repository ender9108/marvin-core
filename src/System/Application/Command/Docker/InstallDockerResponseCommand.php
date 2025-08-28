<?php

namespace App\System\Application\Command\Docker;

use EnderLab\MarvinManagerBundle\List\ManagerMessageReference;
use EnderLab\MarvinManagerBundle\Messenger\Attribute\AsMessageType;
use EnderLab\MarvinManagerBundle\Messenger\ManagerResponseCommand;

#[AsMessageType(binding: ManagerMessageReference::RESPONSE_INSTALL_DOCKER->value)]
class InstallDockerResponseCommand extends ManagerResponseCommand
{

}
