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

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Role;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use Override;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class UserFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        [
            'firstname' => 'Marvin',
            'lastname' => 'Domotic',
            'email' => 'marvin.domotic@marvin.test',
            'roles' => [Role::SUPER_ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::ENABLED,
            'type' => UserType::CLI,
            'locale' => 'fr',
            'theme' => 'dark',
            'timezone' => 'Europe/Paris',
        ],
        [
            'firstname' => 'Administrator',
            'lastname' => 'Administrator',
            'email' => 'administrator@marvin.test',
            'roles' => [Role::SUPER_ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::ENABLED,
            'type' => UserType::APP,
            'locale' => 'fr',
            'theme' => 'dark',
            'timezone' => 'Europe/Paris',
        ],
        [
            'firstname' => 'Johnny',
            'lastname' => 'Begood',
            'email' => 'johnny.begood@marvin.test',
            'roles' => [Role::ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::ENABLED,
            'type' => UserType::APP,
            'locale' => 'fr',
            'theme' => 'dark',
            'timezone' => 'Europe/Paris',
        ],
        [
            'firstname' => 'Alexandre',
            'lastname' => 'Berthelot',
            'email' => 'darkender91@gmail.com',
            'roles' => [Role::SUPER_ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::ENABLED,
            'type' => UserType::APP,
            'locale' => 'fr',
            'theme' => 'dark',
            'timezone' => 'Europe/Paris',
        ],
        [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@test.com',
            'roles' => [Role::USER],
            'password' => 'Test123456789',
            'status' => UserStatus::ENABLED,
            'type' => UserType::APP,
            'locale' => 'fr',
            'theme' => 'dark',
            'timezone' => 'Europe/Paris',
        ],
    ];

    public function __construct(
        private readonly PasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function defaults(): array|callable
    {
        return [];
    }

    #[Override]
    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function (array $parameters): array {
                $parameters['firstname'] = new Firstname($parameters['firstname']);
                $parameters['lastname'] = new Lastname($parameters['lastname']);
                $parameters['email'] = new Email($parameters['email']);
                $parameters['roles'] = new Roles($parameters['roles']);
                $parameters['locale'] = new Locale($parameters['locale']);
                $parameters['theme'] = new Theme($parameters['theme']);
                $parameters['timezone'] = new Timezone($parameters['timezone']);

                return $parameters;
            })
            ->afterInstantiate(function (User $user): void {
                $user->definePassword($user->password, $this->passwordHasher);
            })
        ;
    }

    public static function getDatas(): array
    {
        return self::$datas;
    }

    public static function class(): string
    {
        return User::class;
    }
}
