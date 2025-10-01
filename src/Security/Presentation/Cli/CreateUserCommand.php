<?php

namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\Shared\Domain\ValueObject\Theme;
use Marvin\Shared\Infrastructure\Framework\Symfony\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:security:create-user',
    description: 'Create a new user',
)]
final readonly class CreateUserCommand
{
    public function __construct(
        private SyncCommandBusInterface $commandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'email')]
        string $email,
        #[Argument(name: 'firstname')]
        string $firstname,
        #[Argument(name: 'lastname')]
        string $lastname,
        #[Argument(name: 'roles')]
        string $roleReference,
        #[Argument(name: 'locale')]
        string $locale,
        #[Argument(name: 'theme')]
        string $theme,
        #[Argument(name: 'type')]
        string $type,
        #[Argument(name: 'password')]
        string $password,
    ): int {
        try {
            $roles = match ($roleReference) {
                'user' => Roles::user(),
                'admin' => Roles::admin(),
                'superAdmin' => Roles::superAdmin(),
            };

            $user = $this->commandBus->handle(new CreateUser(
                new Email($email),
                new Firstname($firstname),
                new Lastname($lastname),
                $roles,
                new Locale($locale),
                new Theme($theme),
                new Reference($type),
                $password,
            ));

            $io->success(sprintf('User %s created successfully.', $email));

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $message = $this->exceptionMessageManager->cliResponseFormat($de);
            $io->error($message);

            return Command::FAILURE;
        }
    }
}
