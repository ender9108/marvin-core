<?php

namespace Marvin\Security\Application\CommandHandler\User;

use Marvin\Security\Application\Command\User\ChangePasswordUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ChangePasswordUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(ChangePasswordUser $command): User
    {
        $user = $this->userRepository->byId($command->id);
        $user->changePassword(
            $command->currentPassword,
            $command->newPassword,
            $this->passwordHasher
        );

        $this->userRepository->save($user);

        return $user;
    }
}
