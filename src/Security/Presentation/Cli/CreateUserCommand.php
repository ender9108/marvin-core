<?php
namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Reference;
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
        #[Argument(name: 'type')]
        string $type,
        #[Argument(name: 'password')]
        string $password,
    ): int {
        try {
            $roles = match($roleReference) {
                'user' => Roles::user(),
                'admin' => Roles::admin(),
                'superAdmin' => Roles::superAdmin(),
            };

            $user = $this->commandBus->handle(new CreateUser(
                new Email($email),
                new Firstname($firstname),
                new Lastname($lastname),
                $roles,
                new Reference($type),
                $password,
            ));

            $io->success(sprintf('User %s created successfully.', $user->email->email));

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($de->getMessage());

            return Command::FAILURE;
        }
    }
}
