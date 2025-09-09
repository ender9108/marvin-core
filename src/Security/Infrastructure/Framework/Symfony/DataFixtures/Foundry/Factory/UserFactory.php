<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Security\Domain\List\Role;
use Marvin\Security\Domain\List\UserTypeReference;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\Model\UserType;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Shared\Domain\ValueObject\Email;
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
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserTypeReference::TYPE_CLI->value,
        ],
        [
            'firstname' => 'Administrator',
            'lastname' => 'Administrator',
            'email' => 'administrator@marvin.test',
            'roles' => [Role::SUPER_ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserTypeReference::TYPE_APPLICATION->value,
        ],
        [
            'firstname' => 'Johnny',
            'lastname' => 'Begood',
            'email' => 'johnny.begood@marvin.test',
            'roles' => [Role::ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserTypeReference::TYPE_APPLICATION->value,
        ],
        [
            'firstname' => 'Alexandre',
            'lastname' => 'Berthelot',
            'email' => 'darkender91@gmail.com',
            'roles' => [Role::SUPER_ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserTypeReference::TYPE_APPLICATION->value,
        ],
        [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@test.com',
            'roles' => [Role::USER],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserTypeReference::TYPE_APPLICATION->value,
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

    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function(array $parameters): array {
                $parameters['firstname'] = new Firstname($parameters['firstname']);
                $parameters['lastname'] = new Lastname($parameters['lastname']);
                $parameters['email'] = new Email($parameters['email']);
                $parameters['roles'] = new Roles($parameters['roles']);

                return $parameters;
            })
            ->afterInstantiate(function(User $user) {
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
