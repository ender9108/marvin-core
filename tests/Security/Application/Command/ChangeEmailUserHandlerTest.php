<?php

namespace MarvinTests\Security\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Security\Application\Command\User\ChangeEmailUser;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\UserFactory;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ChangeEmailUserHandlerTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    public function test_change_email_updates_user_email(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $container->get(SyncCommandBusInterface::class);
        /** @var UserRepositoryInterface $users */
        $users = $container->get(UserRepositoryInterface::class);

        $proxy = UserFactory::createOne([
            'firstname' => 'Email',
            'lastname' => 'Changer',
            'email' => 'old.email@marvin.test',
            'roles' => [],
            'password' => 'Test123456789',
            'status' => 'ENABLED',
            'type' => 'APP',
            'locale' => 'fr',
            'theme' => 'dark',
            'timezone' => 'Europe/Paris',
        ]);

        $user = $proxy->_real();
        $id = new UserId($user->id->toString());
        $newEmail = new Email('new.email@marvin.test');

        $bus->handle(new ChangeEmailUser($id, $newEmail));

        $reloaded = $users->byId($id);
        self::assertSame($newEmail->value, $reloaded->email->value);
    }
}
