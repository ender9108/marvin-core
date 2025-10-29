<?php

namespace Marvin\Security\Application\CommandHandler\User;

use Marvin\Security\Application\Command\User\EnableUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class EnableUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(EnableUser $command): User
    {
        $user = $this->userRepository->byId($command->id);
        $user->enable();
        $this->userRepository->save($user);

        return $user;
    }
}
