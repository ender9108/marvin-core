<?php

namespace Marvin\System\Application\CommandHandler;

use EnderLab\MarvinManagerBundle\Messenger\Response\ManagerResponseCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ManagerResponseHandler
{
    public function __invoke(ManagerResponseCommand $response): void {
        dump($response->correlationId, $response->action);
    }
}
