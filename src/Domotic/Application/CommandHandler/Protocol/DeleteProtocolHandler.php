<?php

namespace Marvin\Domotic\Application\CommandHandler\Protocol;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Domotic\Application\Command\Protocol\DeleteProtocol;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteProtocolHandler implements SyncCommandHandlerInterface
{
    public function __construct()
    {
    }

    public function __invoke(DeleteProtocol $command): void
    {
        // TODO: implement handler logic
    }
}
