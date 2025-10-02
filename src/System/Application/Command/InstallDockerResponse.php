<?php

namespace Marvin\System\Application\Command;

use EnderLab\MarvinManagerBundle\System\Infrastructure\Framework\Symfony\Messenger\Attribute\AsMessageType;
use EnderLab\MarvinManagerBundle\System\Infrastructure\Framework\Symfony\Messenger\ManagerRequestCommand;
use EnderLab\MarvinManagerBundle\System\Infrastructure\Framework\Symfony\Messenger\ManagerResponseCommand;
use EnderLab\MarvinManagerBundle\System\Infrastructure\List\ManagerMessageReference;
use Symfony\Component\Validator\Constraints as Assert;

#[AsMessageType(binding: ManagerMessageReference::RESPONSE_INSTALL_DOCKER->value)]
class InstallDockerResponse extends ManagerResponseCommand
{
}
