<?php

namespace Marvin\Security\Application\CommandHandler\User;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Security\Application\Command\User\ResetPasswordUser;
use Marvin\Security\Domain\Exception\RequestResetPasswordAlreadyUsed;
use Marvin\Security\Domain\Exception\RequestResetPasswordExpired;
use Marvin\Security\Domain\Repository\RequestResetPasswordRepositoryInterface;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ResetPasswordUserHandler
{
    public function __construct(
        private RequestResetPasswordRepositoryInterface $resetPasswordRepository,
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher
    ) {
    }

    public function __invoke(ResetPasswordUser $command): void
    {
        $now = new DateTimeImmutable();
        $request = $this->resetPasswordRepository->byToken($command->token);

        if ($now > $request->expiresAt) {
            throw RequestResetPasswordExpired::withToken($command->token);
        }

        if ($request->isUsed()) {
            throw RequestResetPasswordAlreadyUsed::withToken($command->token);
        }

        $request->user->resetPassword(
            $command->password,
            $this->passwordHasher
        );
        $request->markAsUsed();
        $this->userRepository->save($request->user);
        $this->resetPasswordRepository->save($request);
    }
}
