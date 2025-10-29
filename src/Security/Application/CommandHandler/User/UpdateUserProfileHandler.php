<?php

namespace Marvin\Security\Application\CommandHandler\User;

use Marvin\Security\Application\Command\User\UpdateProfileUser;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateUserProfileHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(UpdateProfileUser $command): void
    {
        $user = $this->userRepository->byId($command->id);
        $user->updateProfile(
            $command->firstname,
            $command->lastname,
            $command->roles,
            $command->theme,
            $command->locale,
            $command->timezone,
        );
        $this->userRepository->save($user);
    }
}
