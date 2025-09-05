<?php
namespace Marvin\Security\Application\CommandHandler\User;

use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Repository\UserStatusRepositoryInterface;
use Marvin\Security\Domain\Repository\UserTypeRepositoryInterface;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\Service\UniqueEmailVerifier;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\Security\Domain\Model\UserStatus;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateUserHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserStatusRepositoryInterface $userStatusRepository,
        private UserTypeRepositoryInterface $userTypeRepository,
        private PasswordHasherInterface $passwordHasher,
        private UniqueEmailVerifier $uniqueEmailVerifier
    ) {
    }

    public function __invoke(CreateUser $command): User {
        $this->uniqueEmailVerifier->verify($command->email);

        $type = $this->userTypeRepository->byReference($command->type);
        $statusEnabled = $this->userStatusRepository->byReference(new Reference(UserStatus::STATUS_ENABLED));
        $user = User::create(
            $command->email,
            $command->firstName,
            $command->lastName,
            $statusEnabled,
            $type,
            $command->roles
        );
        $user->definePassword($command->password, $this->passwordHasher);
        $this->userRepository->save($user);

        return $user;
    }
}
