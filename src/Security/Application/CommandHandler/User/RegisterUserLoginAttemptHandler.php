<?php

namespace Marvin\Security\Application\CommandHandler\User;

use Marvin\Security\Application\Command\User\LockUser;
use Marvin\Security\Application\Command\User\RegisterUserLoginAttempt;
use Marvin\Security\Domain\Model\LoginAttempt;
use Marvin\Security\Domain\Repository\LoginAttemptRepositoryInterface;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Model\UserStatus;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'sync.command.bus')]
final readonly class RegisterUserLoginAttemptHandler implements SyncCommandHandlerInterface
{
    private const int ATTEMPTS_LIMIT = 3;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoginAttemptRepositoryInterface $loginAttemptRepository,
        private SyncCommandBusInterface $commandBus
    ) {
    }

    public function __invoke(RegisterUserLoginAttempt $command): void
    {
        $user = $this->userRepository->byId($command->id);
        $attempts = $this->loginAttemptRepository->countBy($user);

        if ($attempts < self::ATTEMPTS_LIMIT) {
            $this->loginAttemptRepository->save(LoginAttempt::create($user));
        } else {
            if ($user->status->reference->reference !== UserStatus::STATUS_LOCKED) {
                $this->commandBus->handle(new LockUser($user->id));
            }
        }
    }
}
