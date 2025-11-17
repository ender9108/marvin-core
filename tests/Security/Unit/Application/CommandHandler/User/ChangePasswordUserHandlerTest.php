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

use Marvin\Security\Application\Command\User\ChangePasswordUser;
use Marvin\Security\Application\CommandHandler\User\ChangePasswordUserHandler;
use Marvin\Security\Domain\Exception\InvalidCurrentPassword;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
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

final class ChangePasswordUserHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private PasswordHasherInterface|MockObject $passwordHasher;
    private ChangePasswordUserHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(PasswordHasherInterface::class);

        $this->handler = new ChangePasswordUserHandler(
            $this->userRepository,
            $this->passwordHasher
        );
    }

    public function testSuccessfulPasswordChange(): void
    {
        $userId = new UserId();
        $command = new ChangePasswordUser(
            $userId,
            'OldPassword123!',
            'NewPassword456!'
        );

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

        // Set initial password using a mock hasher to avoid calling the real hash method
        $initialPasswordHasher = $this->createMock(PasswordHasherInterface::class);
        $initialPasswordHasher
            ->method('hash')
            ->with($user, 'OldPassword123!')
            ->willReturn('hashed_old_password');
        $user->definePassword('OldPassword123!', $initialPasswordHasher);

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willReturn($user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('verify')
            ->with($user, 'OldPassword123!')
            ->willReturn(true);

        $this->passwordHasher
            ->expects($this->once())
            ->method('hash')
            ->with($user, 'NewPassword456!')
            ->willReturn('hashed_new_password');

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $result = ($this->handler)($command);

        $this->assertInstanceOf(User::class, $result);
    }

    public function testThrowsExceptionWhenUserNotFound(): void
    {
        $userId = new UserId();
        $command = new ChangePasswordUser(
            $userId,
            'OldPassword123!',
            'NewPassword456!'
        );

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willThrowException(UserNotFound::withId($userId));

        $this->expectException(UserNotFound::class);

        ($this->handler)($command);
    }

    public function testThrowsExceptionWhenCurrentPasswordIsInvalid(): void
    {
        $userId = new UserId();
        $command = new ChangePasswordUser(
            $userId,
            'WrongPassword!',
            'NewPassword456!'
        );

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

        // Set initial password using a mock hasher to avoid calling the real hash method
        $initialPasswordHasher = $this->createMock(PasswordHasherInterface::class);
        $initialPasswordHasher
            ->method('hash')
            ->with($user, 'CorrectPassword123!')
            ->willReturn('hashed_correct_password');
        $user->definePassword('CorrectPassword123!', $initialPasswordHasher);

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willReturn($user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('verify')
            ->with($user, 'WrongPassword!')
            ->willReturn(false);

        $this->expectException(InvalidCurrentPassword::class);

        ($this->handler)($command);
    }
}
