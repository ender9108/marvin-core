<?php

namespace Marvin\Security\Application\CommandHandler\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Security\Application\Command\User\LockUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Repository\UserStatusRepositoryInterface;
use Marvin\Security\Domain\Service\BeforeDeleteOrUpdateStatusUserVerifier;
use Marvin\Shared\Domain\ValueObject\Reference;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class LockUserHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserStatusRepositoryInterface $userStatusRepository,
    ) {
    }

    public function __invoke(LockUser $command): User
    {
        $user = $this->userRepository->byId($command->id);
        $lockStatus = $this
            ->userStatusRepository
            ->byReference(new Reference(UserStatus::STATUS_LOCKED))
        ;

        $user->lockUser($lockStatus);
        $this->userRepository->save($user);

        return $user;
    }
}
