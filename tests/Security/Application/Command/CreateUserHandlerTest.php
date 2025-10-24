<?php

namespace MarvinTests\Security\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class CreateUserHandlerTest extends KernelTestCase
{
    use ResetDatabase;

    public function test_create_user_command_persists_user(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $container->get(SyncCommandBusInterface::class);
        /** @var UserRepositoryInterface $users */
        $users = $container->get(UserRepositoryInterface::class);

        $email = new Email('new.user@marvin.test');
        $firstname = new Firstname('New');
        $lastname = new Lastname('User');
        $roles = Roles::user();
        $locale = Locale::fr();
        $theme = Theme::dark();
        $type = new UserType(UserType::TYPES['APP']);
        $timezone = new Timezone('Europe/Paris');
        $password = 'Test123456789';

        $bus->handle(new CreateUser(
            $email,
            $firstname,
            $lastname,
            $roles,
            $locale,
            $theme,
            $type,
            $timezone,
            $password,
        ));

        $created = $users->byEmail($email);
        self::assertNotNull($created, 'User should have been persisted');
        self::assertSame($email->value, $created->email->value);
        self::assertNotNull($created->password, 'Password should be hashed and stored');
        self::assertNotSame($password, $created->password, 'Password must not be stored in clear text');
    }
}
