<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\Model\UserType;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Role;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Shared\Domain\ValueObject\Email;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class UserFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        [
            'firstName' => 'Marvin',
            'lastName' => 'Domotic',
            'email' => 'marvin.domotic@marvin.test',
            'roles' => [Role::SUPER_ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserType::TYPE_CLI,
        ],
        [
            'firstName' => 'Administrator',
            'lastName' => 'Administrator',
            'email' => 'administrator@marvin.test',
            'roles' => [Role::SUPER_ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserType::TYPE_APPLICATION,
        ],
        [
            'firstName' => 'Johnny',
            'lastName' => 'Begood',
            'email' => 'johnny.begood@marvin.test',
            'roles' => [Role::ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserType::TYPE_APPLICATION,
        ],
        [
            'firstName' => 'Alexandre',
            'lastName' => 'Berthelot',
            'email' => 'darkender91@gmail.com',
            'roles' => [Role::SUPER_ADMIN],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserType::TYPE_APPLICATION,
        ],
        [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@test.com',
            'roles' => [Role::USER],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserType::TYPE_APPLICATION,
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
                $parameters['firstName'] = new Firstname($parameters['firstName']);
                $parameters['lastName'] = new Lastname($parameters['lastName']);
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
