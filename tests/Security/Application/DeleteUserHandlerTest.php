<?php

namespace MarvinTests\Security\Application;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Security\Application\Command\User\DeleteUser;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DeleteUserHandlerTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    public function test_delete_user_removes_entity(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $container->get(SyncCommandBusInterface::class);
        /** @var UserRepositoryInterface $users */
        $users = $container->get(UserRepositoryInterface::class);

        $proxy = UserFactory::createOne([
            'firstname' => 'To',
            'lastname' => 'Delete',
            'email' => 'to.delete@marvin.test',
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

        $bus->handle(new DeleteUser($id));

        $this->expectException(UserNotFound::class);
        $users->byId($id);
    }
}
