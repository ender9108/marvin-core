<?php

namespace Marvin\System\Application\CommandHandler;

use Marvin\System\Application\Command\InstallDockerResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class InstallDockerResponseHandler
{
    public function __invoke(InstallDockerResponse $message): void
    {
        dump($message);
    }
}
