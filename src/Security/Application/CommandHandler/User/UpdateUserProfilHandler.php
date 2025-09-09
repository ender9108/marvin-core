<?php
namespace Marvin\Security\Application\CommandHandler\User;

use Marvin\Security\Application\Command\User\UpdateUserProfil;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'sync.command.bus')]
final readonly class UpdateUserProfilHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(UpdateUserProfil $command): void {
        $user = $this->userRepository->byId($command->id);
        $user->updateProfile(
            $command->firstname,
            $command->lastname,
            $command->roles
        );
        $this->userRepository->save($user);
    }
}
