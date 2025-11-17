<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Security\Application\CommandHandler\User;

use Marvin\Security\Application\Command\User\DeleteUser;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\LastUserAdminVerifierInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LastUserAdminVerifierInterface $beforeDeleteOrUpdateStatusUserVerifier,
    ) {
    }

    public function __invoke(DeleteUser $command): void
    {
        $user = $this->userRepository->byId($command->id);
        $this->beforeDeleteOrUpdateStatusUserVerifier->verify($user);
        $user->delete();
        $this->userRepository->remove($user);
    }
}
