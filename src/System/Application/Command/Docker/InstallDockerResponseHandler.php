<?php

namespace App\System\Application\Command\Docker;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class InstallDockerResponseHandler
{
    public function __invoke(InstallDockerResponseCommand $message): void
    {
        dump($message);
    }
}
