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

use Marvin\Security\Application\Command\User\UpdateProfileUser;
use Marvin\Security\Application\CommandHandler\User\UpdateUserProfileHandler;
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

final class UpdateUserProfileHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private UpdateUserProfileHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->handler = new UpdateUserProfileHandler($this->userRepository);
    }

    public function testSuccessfulProfileUpdate(): void
    {
        $userId = new UserId();
        $command = new UpdateProfileUser(
            $userId,
            Firstname::fromString('Jane'),
            Lastname::fromString('Smith'),
            Roles::admin(),
            Locale::fromString('fr'),
            Theme::fromString('dark'),
            Timezone::fromString('Europe/Paris')
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

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $result = ($this->handler)($command);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Jane', $result->firstname->value);
        $this->assertEquals('Smith', $result->lastname->value);
        $this->assertEquals('fr', $result->locale->value);
        $this->assertEquals('dark', $result->theme->value);
        $this->assertEquals('Europe/Paris', $result->timezone->value);
    }

    public function testSuccessfulPartialProfileUpdate(): void
    {
        $userId = new UserId();
        $command = new UpdateProfileUser(
            $userId,
            Firstname::fromString('Jane'),
            null,
            null,
            null,
            null,
            null
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

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $result = ($this->handler)($command);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Jane', $result->firstname->value);
        $this->assertEquals('Doe', $result->lastname->value); // Unchanged
    }

    public function testThrowsExceptionWhenUserNotFound(): void
    {
        $userId = new UserId();
        $command = new UpdateProfileUser(
            $userId,
            Firstname::fromString('Jane'),
            null,
            null,
            null,
            null,
            null
        );

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willThrowException(UserNotFound::withId($userId));

        $this->expectException(UserNotFound::class);

        ($this->handler)($command);
    }
}
