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

use Marvin\Security\Application\Command\User\LockUser;
use Marvin\Security\Application\CommandHandler\User\LockUserHandler;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Model\User;
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

final class LockUserHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private LockUserHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->handler = new LockUserHandler($this->userRepository);
    }

    public function testSuccessfulUserLocking(): void
    {
        $userId = new UserId();
        $command = new LockUser($userId);

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

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        ($this->handler)($command);

        $this->assertEquals(UserStatus::LOCKED, $user->status);
    }

    public function testThrowsExceptionWhenUserNotFound(): void
    {
        $userId = new UserId();
        $command = new LockUser($userId);

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willThrowException(UserNotFound::withId($userId));

        $this->expectException(UserNotFound::class);

        ($this->handler)($command);
    }
}
