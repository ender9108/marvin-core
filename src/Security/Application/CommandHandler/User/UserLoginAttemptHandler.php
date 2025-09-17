<?php

namespace Marvin\Security\Application\CommandHandler\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Security\Application\Command\User\LockUser;
use Marvin\Security\Application\Command\User\UserLoginAttempt;
use Marvin\Security\Domain\Model\LoginAttempt;
use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\Repository\LoginAttemptRepositoryInterface;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UserLoginAttemptHandler implements SyncCommandHandlerInterface
{
    private const int ATTEMPTS_LIMIT = 3;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoginAttemptRepositoryInterface $loginAttemptRepository,
        private SyncCommandBusInterface $commandBus
    ) {
    }

    public function __invoke(UserLoginAttempt $command): void
    {
        $user = $this->userRepository->byId($command->id);
        $attempts = $this->loginAttemptRepository->countBy($user);

        if ($attempts < self::ATTEMPTS_LIMIT) {
            $this->loginAttemptRepository->save(LoginAttempt::create($user));
        } else {
            if ($user->status->reference->value !== UserStatus::STATUS_LOCKED) {
                $this->commandBus->handle(new LockUser($user->id));
            }
        }
    }
}
