<?php

namespace Marvin\Security\Application\CommandHandler\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Security\Application\Command\User\DisableUser;
use Marvin\Security\Domain\List\UserStatusReference;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Repository\UserStatusRepositoryInterface;
use Marvin\Security\Domain\Service\BeforeDeleteOrUpdateStatusUserVerifier;
use Marvin\Shared\Domain\ValueObject\Reference;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DisableUserHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserStatusRepositoryInterface $userStatusRepository,
        private BeforeDeleteOrUpdateStatusUserVerifier $beforeDeleteOrUpdateStatusUserVerifier,
    ) {
    }

    public function __invoke(DisableUser $command): User
    {
        $user = $this->userRepository->byId($command->id);
        $this->beforeDeleteOrUpdateStatusUserVerifier->verify($user);
        $disableStatus = $this
            ->userStatusRepository
            ->byReference(new Reference(UserStatusReference::STATUS_DISABLED->value))
        ;
        $user->disableUser($disableStatus);
        $this->userRepository->save($user);

        return $user;
    }
}
