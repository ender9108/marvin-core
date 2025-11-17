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

namespace Marvin\Tests\Security\Unit\Application\CommandHandler\User;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Security\Application\Command\User\UserLoginAttempt;
use Marvin\Security\Application\CommandHandler\User\UserLoginAttemptHandler;
use Marvin\Security\Domain\Model\LoginAttempt;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\LoginAttemptRepositoryInterface;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserLoginAttemptHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private LoginAttemptRepositoryInterface|MockObject $loginAttemptRepository;
    private SyncCommandBusInterface|MockObject $commandBus;
    private UserLoginAttemptHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->loginAttemptRepository = $this->createMock(LoginAttemptRepositoryInterface::class);
        $this->commandBus = $this->createMock(SyncCommandBusInterface::class);

        $this->handler = new UserLoginAttemptHandler(
            $this->userRepository,
            $this->loginAttemptRepository,
            $this->commandBus
        );
    }

    public function testSuccessfulLoginAttemptRecording(): void
    {
        $userId = new UserId();
        $command = new UserLoginAttempt($userId);

        $user = User::create(
            Email::fromString('test@example.com'),
            Firstname::fromString('John'),
            Lastname::fromString('Doe'),
            UserStatus::enabled(),
            UserType::APP,
            Timezone::fromString('UTC'),
            Roles::user(),
            Locale::fromString('en'),
            Theme::fromString('light')
        );

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willReturn($user);

        $this->loginAttemptRepository
            ->expects($this->once())
            ->method('countBy')
            ->with($user)
            ->willReturn(1); // Less than 3 attempts

        $this->loginAttemptRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(LoginAttempt::class));

        ($this->handler)($command);
    }

    public function testFailedLoginAttemptRecording(): void
    {
        $userId = new UserId();
        $command = new UserLoginAttempt($userId);

        $user = User::create(
            Email::fromString('test@example.com'),
            Firstname::fromString('John'),
            Lastname::fromString('Doe'),
            UserStatus::enabled(),
            UserType::APP,
            Timezone::fromString('UTC'),
            Roles::user(),
            Locale::fromString('en'),
            Theme::fromString('light')
        );

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willReturn($user);

        $this->loginAttemptRepository
            ->expects($this->once())
            ->method('countBy')
            ->with($user)
            ->willReturn(3); // 3 attempts, should lock user

        $this->commandBus
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($cmd) use ($user) {
                return $cmd instanceof \Marvin\Security\Application\Command\User\LockUser
                    && $cmd->id->toString() === $user->id->toString();
            }));

        ($this->handler)($command);
    }
}
