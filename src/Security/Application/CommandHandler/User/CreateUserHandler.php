<?php

namespace Marvin\Security\Application\CommandHandler\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\Service\UniqueEmailVerifierInterface;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateUserHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private UniqueEmailVerifierInterface $uniqueEmailVerifier
    ) {
    }

    public function __invoke(CreateUser $command): User
    {
        $this->uniqueEmailVerifier->verify($command->email);
        $user = User::create(
            $command->email,
            $command->firstname,
            $command->lastname,
            UserStatus::enabled(),
            $command->type,
            $command->timezone,
            $command->roles,
            $command->locale,
            $command->theme,
        );
        $user->definePassword($command->password, $this->passwordHasher);
        $this->userRepository->save($user);

        return $user;
    }
}
