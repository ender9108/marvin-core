<?php

namespace Marvin\Security\Application\CommandHandler\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Security\Application\Command\User\DeleteUser;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\LastUserAdminVerifierInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LastUserAdminVerifierInterface $beforeDeleteOrUpdateStatusUserVerifier,
    ) {
    }

    public function __invoke(DeleteUser $command): void
    {
        $user = $this->userRepository->byId($command->id);
        $this->beforeDeleteOrUpdateStatusUserVerifier->verify($user);
        $user->delete();
        $this->userRepository->remove($user);
    }
}
