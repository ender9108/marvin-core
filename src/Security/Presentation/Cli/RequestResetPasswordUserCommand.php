<?php

namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Security\Application\Command\User\ChangeEmailUser;
use Marvin\Security\Application\Command\User\ChangePasswordUser;
use Marvin\Security\Application\Command\User\DisableUser;
use Marvin\Security\Application\Command\User\EnableUser;
use Marvin\Security\Application\Command\User\RequestResetPasswordUser;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Infrastructure\Framework\Symfony\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:security:request-reset-password-user',
    description: 'Change user password',
)]
final readonly class RequestResetPasswordUserCommand
{
    public function __construct(
        private SyncCommandBusInterface $commandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'mail')]
        string $mail,
    ): int {
        try {
            $this->commandBus->handle(new RequestResetPasswordUser(new Email($mail), ));

            $io->success('Request reset password successfully.');

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }
    }
}
