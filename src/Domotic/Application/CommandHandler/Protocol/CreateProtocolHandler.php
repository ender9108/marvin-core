<?php

namespace Marvin\Domotic\Application\CommandHandler\Protocol;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Domotic\Application\Command\Protocol\CreateProtocol;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateProtocolHandler implements SyncCommandHandlerInterface
{
    public function __construct()
    {
    }

    public function __invoke(CreateProtocol $command): void
    {
        // TODO: implement handler logic
    }
}
