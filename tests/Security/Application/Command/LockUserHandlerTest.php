<?php

namespace MarvinTests\Security\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Security\Application\Command\User\LockUser;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\UserFactory;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LockUserHandlerTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    public function test_lock_user_changes_status(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $container->get(SyncCommandBusInterface::class);
        /** @var UserRepositoryInterface $users */
        $users = $container->get(UserRepositoryInterface::class);

        $proxy = UserFactory::createOne([
            'firstname' => 'Lock',
            'lastname' => 'Me',
            'email' => 'lock.me@marvin.test',
            'roles' => [],
            'password' => 'Test123456789',
            'status' => UserStatus::STATUSES['ENABLED'],
            'type' => 'APP',
            'locale' => 'fr',
            'theme' => 'dark',
            'timezone' => 'Europe/Paris',
        ]);

        $user = $proxy->_real();

        $bus->handle(new LockUser(new UserId($user->id->toString())));

        $reloaded = $users->byId(new UserId($user->id->toString()));
        self::assertSame(UserStatus::STATUSES['LOCKED'], $reloaded->status->value);
    }
}
