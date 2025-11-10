<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\CommandHandler;

use Marvin\Protocol\Application\Command\DisableProtocol;
use Marvin\Protocol\Domain\Repository\ProtocolRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DisableProtocolHandler
{
    public function __construct(
        private ProtocolRepositoryInterface $protocolRepository,
    ) {
    }

    public function __invoke(DisableProtocol $command): void
    {
        $protocol = $this->protocolRepository->byId($command->protocolId);

        $protocol->disconnect();

        $this->protocolRepository->save($protocol);
    }
}
