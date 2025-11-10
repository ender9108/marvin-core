<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\CommandHandler;

use Marvin\Protocol\Application\Command\RegisterProtocol;
use Marvin\Protocol\Domain\Model\Protocol;
use Marvin\Protocol\Domain\Repository\ProtocolRepositoryInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RegisterProtocolHandler
{
    public function __construct(
        private ProtocolRepositoryInterface $protocolRepository,
    ) {
    }

    public function __invoke(RegisterProtocol $command): void
    {
        $protocol = Protocol::register(
            name: $command->name,
            transportType: $command->type,
            configuration: $command->configuration,
            preferredExecutionMode: $command->preferredExecutionMode ?? ExecutionMode::DEVICE_LOCK,
        );

        $this->protocolRepository->save($protocol);
    }
}
