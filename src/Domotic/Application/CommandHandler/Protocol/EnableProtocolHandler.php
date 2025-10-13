<?php

namespace Marvin\Domotic\Application\CommandHandler\Protocol;

use Marvin\Domotic\Application\Command\Protocol\EnableProtocol;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Domotic\Domain\Model\Protocol;
use Marvin\Domotic\Domain\Repository\ProtocolRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class EnableProtocolHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ProtocolRepositoryInterface $protocolRepository,
    ) {
    }

    public function __invoke(EnableProtocol $command): Protocol
    {
        $protocol = $this->protocolRepository->byId($command->id);
        $protocol->enable();

        $this->protocolRepository->save($protocol);

        return $protocol;
    }
}
