<?php

namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Security\Application\Command\User\ChangeEmailUser;
use Marvin\Security\Application\Command\User\DisableUser;
use Marvin\Security\Application\Command\User\EnableUser;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Infrastructure\Framework\Symfony\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:security:change-email-user',
    description: 'Change user email',
)]
final class ChangeEmailUserCommand
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
        #[Argument(name: 'email')]
        string $email,
    ): int {
        try {
            $this->commandBus->handle(new ChangeEmailUser(
                new UserId($id),
                new Email($email)
            ));

            $io->success('Update user email successfully.');

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->getMessage($de));

            return Command::FAILURE;
        }
    }
}
