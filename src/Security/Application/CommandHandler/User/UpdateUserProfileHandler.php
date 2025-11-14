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

use Marvin\Security\Application\Command\User\UpdateProfileUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateUserProfileHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(UpdateProfileUser $command): User
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

        return $user;
    }
}
