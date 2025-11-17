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

use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\Service\UniqueEmailVerifierInterface;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private UniqueEmailVerifierInterface $uniqueEmailVerifier
    ) {
    }

    public function __invoke(CreateUser $command): User
    {
        $this->uniqueEmailVerifier->verify($command->email);
        $user = User::create(
            $command->email,
            $command->firstname,
            $command->lastname,
            UserStatus::enabled(),
            UserType::APP,
            $command->timezone,
            $command->roles,
            $command->locale,
            $command->theme,
        );
        $user->definePassword($command->password, $this->passwordHasher);
        $this->userRepository->save($user);

        return $user;
    }
}
