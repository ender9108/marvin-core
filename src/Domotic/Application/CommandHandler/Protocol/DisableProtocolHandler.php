<?php

namespace Marvin\Domotic\Application\CommandHandler\Protocol;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Domotic\Application\Command\Protocol\DisableProtocol;
use Marvin\Domotic\Domain\Model\Protocol;
use Marvin\Domotic\Domain\Repository\ProtocolRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DisableProtocolHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private ProtocolRepositoryInterface $protocolRepository
    ) {
    }

    public function __invoke(DisableProtocol $command): Protocol
    {
        $protocol = $this->protocolRepository->byId($command->id);
        $protocol->disable();

        $this->protocolRepository->save($protocol);

        return $protocol;
    }
}
