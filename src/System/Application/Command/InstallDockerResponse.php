<?php

namespace App\System\Application\Command;

use EnderLab\MarvinManagerBundle\List\ManagerMessageReference;
use EnderLab\MarvinManagerBundle\Messenger\Attribute\AsMessageType;
use EnderLab\MarvinManagerBundle\Messenger\ManagerRequestMessage;
use EnderLab\MarvinManagerBundle\Messenger\ManagerResponseMessage;
use Symfony\Component\Validator\Constraints as Assert;

#[AsMessageType(binding: ManagerMessageReference::RESPONSE_INSTALL_DOCKER)]
class InstallDockerResponse extends ManagerResponseMessage
{

}
