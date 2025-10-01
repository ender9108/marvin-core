<?php

namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Security\Application\Command\User\DisableUser;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Infrastructure\Framework\Symfony\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:security:disable-user',
    description: 'Disable a user',
)]
final readonly class DisableUserCommand
{
    public function __construct(
        private SyncCommandBusInterface $commandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'id')]
        string $id,
    ): int {
        try {
            $this->commandBus->handle(new DisableUser(new UserId($id)));

            $io->success('User disabled successfully.');

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }
    }
}
