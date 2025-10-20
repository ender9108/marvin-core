<?php

namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Security\Application\Command\User\ChangePasswordUser;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:security:change-password-user',
    description: 'Change user password',
)]
final readonly class ChangePasswordUserCommand
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
        #[Argument(name: 'currentPassword')]
        string $currentPassword,
        #[Argument(name: 'newPassword')]
        string $newPassword,
    ): int {
        try {
            $this->commandBus->handle(new ChangePasswordUser(
                new UserId($id),
                $currentPassword,
                $newPassword,
            ));

            $io->success('Update user password successfully.');

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }
    }
}
