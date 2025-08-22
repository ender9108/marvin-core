<?php

namespace App\System\Application\Command;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class InstallDockerResponseHandler
{
    public function __invoke(InstallDockerResponse $message): void
    {
        dump($message);
    }
}
