<?php

namespace MarvinTests\Security\Application;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Security\Application\Command\User\ChangePasswordUser;
use Marvin\Security\Domain\Exception\InvalidCurrentPassword;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\UserFactory;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ChangePasswordUserHandlerTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    public function test_change_password_updates_hash(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $container->get(SyncCommandBusInterface::class);
        /** @var UserRepositoryInterface $users */
        $users = $container->get(UserRepositoryInterface::class);

        $plain = 'Test123456789';
        $proxy = UserFactory::createOne([
            'firstname' => 'Pwd',
            'lastname' => 'Change',
            'email' => 'pwd.change@marvin.test',
            'roles' => [],
            'password' => $plain,
            'status' => 'ENABLED',
            'type' => 'APP',
            'locale' => 'fr',
            'theme' => 'dark',
            'timezone' => 'Europe/Paris',
        ]);

        $user = $proxy->_real();
        $oldHash = $user->password;
        $id = new UserId($user->id->toString());

        $bus->handle(new ChangePasswordUser($id, $plain, 'NewPassword123!'));

        $reloaded = $users->byId($id);
        self::assertNotSame($oldHash, $reloaded->password);
        self::assertNotEmpty($reloaded->password);
    }

    public function test_change_password_with_invalid_current_password_throws(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $container->get(SyncCommandBusInterface::class);

        $proxy = UserFactory::createOne([
            'firstname' => 'Pwd',
            'lastname' => 'Invalid',
            'email' => 'pwd.invalid@marvin.test',
            'roles' => [],
            'password' => 'CorrectPassword1',
            'status' => 'ENABLED',
            'type' => 'APP',
            'locale' => 'fr',
            'theme' => 'dark',
            'timezone' => 'Europe/Paris',
        ]);

        $user = $proxy->_real();
        $id = new UserId($user->id->toString());

        $this->expectException(InvalidCurrentPassword::class);
        $bus->handle(new ChangePasswordUser($id, 'WrongPassword', 'NewPassword123!'));
    }
}
