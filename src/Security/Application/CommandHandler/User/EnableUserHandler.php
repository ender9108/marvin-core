<?php

use Marvin\Security\Application\Command\User\EnableUser;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Repository\UserStatusRepositoryInterface;
use Marvin\Security\Domain\Service\BeforeDeleteOrUpdateStatusUserVerifier;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\Security\Domain\Model\UserStatus;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class EnableUserHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserStatusRepositoryInterface $userStatusRepository,
    ) {
    }

    public function __invoke(EnableUser $command): void
    {
        $user = $this->userRepository->byId($command->id);
        $enableStatus = $this
            ->userStatusRepository
            ->byReference(new Reference(UserStatus::STATUS_ENABLED))
        ;

        $user->enableUser($enableStatus);
        $this->userRepository->save($user);
    }
}
