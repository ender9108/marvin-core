<?php

namespace Marvin\Security\Application\CommandHandler\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Security\Application\Command\User\DisableUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\LastUserAdminVerifierInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DisableUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LastUserAdminVerifierInterface $beforeDeleteOrUpdateStatusUserVerifier,
    ) {
    }

    public function __invoke(DisableUser $command): User
    {
        $user = $this->userRepository->byId($command->id);
        $this->beforeDeleteOrUpdateStatusUserVerifier->verify($user);
        $user->disable();
        $this->userRepository->save($user);

        return $user;
    }
}
