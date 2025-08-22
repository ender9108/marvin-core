<?php

namespace App\System\Infrastructure\Foundry\Factory;

use App\System\Domain\Model\User;
use App\System\Domain\Model\UserStatus;
use App\System\Domain\Model\UserType;
use App\System\Infrastructure\Symfony\Security\SecurityUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class UserFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        [
            'firstname' => 'Marvin',
            'lastname' => 'Domotic',
            'email' => 'marvin.domotic@marvin.test',
            'roles' => ['ROLE_SUPER_ADMIN'],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserType::TYPE_COMMAND,
        ],
        [
            'firstname' => 'Administrator',
            'lastname' => 'Administrator',
            'email' => 'administrator@marvin.test',
            'roles' => ['ROLE_SUPER_ADMIN'],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserType::TYPE_APPLICATION,
        ],
        [
            'firstname' => 'Alexandre',
            'lastname' => 'Berthelot',
            'email' => 'darkender91@gmail.com',
            'roles' => ['ROLE_SUPER_ADMIN'],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserType::TYPE_APPLICATION,
        ],
        [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@test.com',
            'roles' => ['ROLE_USER'],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUS_ENABLED,
            'type' => UserType::TYPE_APPLICATION,
        ],
    ];

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
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
            ->afterInstantiate(function(User $user) {
                $securityUser = new SecurityUser();
                $user->setPassword($this->passwordHasher->hashPassword($securityUser, $user->getPassword()));
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
