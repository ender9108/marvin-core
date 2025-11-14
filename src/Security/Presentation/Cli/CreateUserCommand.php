<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
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
        #[Argument(name: 'timezone')]
        string $timezone,
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
                UserType::from($type),
                new Timezone($timezone),
                $password,
            ));

            $io->success(sprintf('User %s created successfully.', $email));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
