<?php

namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use Exception;
use Marvin\Security\Application\Command\User\RequestResetPasswordUser;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:security:request-reset-password-user',
    description: 'Make request reset user password',
)]
final readonly class RequestResetPasswordUserCommand
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'mail')]
        string $mail,
    ): int {
        try {
            $this->commandBus->dispatch(new RequestResetPasswordUser(new Email($mail)));

            $io->success('Request reset password successfully.');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
