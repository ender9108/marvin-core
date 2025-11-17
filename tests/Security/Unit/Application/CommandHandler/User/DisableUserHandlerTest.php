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

use Marvin\Security\Application\Command\User\DisableUser;
use Marvin\Security\Application\CommandHandler\User\DisableUserHandler;
use Marvin\Security\Domain\Exception\LastUserAdmin;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\LastUserAdminVerifierInterface;
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

final class DisableUserHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private LastUserAdminVerifierInterface|MockObject $lastUserAdminVerifier;
    private DisableUserHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->lastUserAdminVerifier = $this->createMock(LastUserAdminVerifierInterface::class);

        $this->handler = new DisableUserHandler(
            $this->userRepository,
            $this->lastUserAdminVerifier
        );
    }

    public function testSuccessfulUserDisabling(): void
    {
        $userId = new UserId();
        $command = new DisableUser($userId);

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

        $this->lastUserAdminVerifier
            ->expects($this->once())
            ->method('verify')
            ->with($user);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        ($this->handler)($command);

        $this->assertEquals(UserStatus::DISABLED, $user->status);
    }

    public function testThrowsExceptionWhenUserNotFound(): void
    {
        $userId = new UserId();
        $command = new DisableUser($userId);

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willThrowException(UserNotFound::withId($userId));

        $this->expectException(UserNotFound::class);

        ($this->handler)($command);
    }

    public function testThrowsExceptionWhenDisablingLastAdmin(): void
    {
        $userId = new UserId();
        $command = new DisableUser($userId);

        $user = User::create(
            Email::fromString('admin@example.com'),
            Firstname::fromString('Admin'),
            Lastname::fromString('User'),
            UserStatus::enabled(),
            UserType::APP,
            Timezone::fromString('UTC'),
            Roles::admin(),
            Locale::fromString('en'),
            Theme::fromString('light')
        );

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willReturn($user);

        $this->lastUserAdminVerifier
            ->expects($this->once())
            ->method('verify')
            ->with($user)
            ->willThrowException(new LastUserAdmin('Cannot disable the last admin user'));

        $this->expectException(LastUserAdmin::class);

        ($this->handler)($command);
    }
}
