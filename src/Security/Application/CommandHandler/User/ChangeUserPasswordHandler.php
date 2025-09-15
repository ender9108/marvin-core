<?php
namespace Marvin\Security\Application\CommandHandler\User;

use Marvin\Security\Application\Command\User\ChangeUserPassword;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ChangeUserPasswordHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(ChangeUserPassword $command): void
    {
        $user = $this->userRepository->byId($command->id);
        $user->updatePassword(
            $command->currentPassword,
            $command->newPassword,
            $this->passwordHasher
        );

        $this->userRepository->save($user);
    }
}
