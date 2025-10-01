<?php

namespace Marvin\Security\Application\CommandHandler\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Security\Application\Command\User\ChangePasswordUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\RequestResetPasswordRepositoryInterface;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ResetPasswordUserHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private RequestResetPasswordRepositoryInterface $resetPasswordRepository,
        private PasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(ChangePasswordUser $command): void
    {
    }
}
