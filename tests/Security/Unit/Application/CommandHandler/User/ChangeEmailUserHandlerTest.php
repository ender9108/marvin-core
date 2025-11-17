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

use Marvin\Security\Application\Command\User\ChangeEmailUser;
use Marvin\Security\Application\CommandHandler\User\ChangeEmailUserHandler;
use Marvin\Security\Domain\Exception\EmailAlreadyUsed;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\UniqueEmailVerifierInterface;
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

final class ChangeEmailUserHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private UniqueEmailVerifierInterface|MockObject $uniqueEmailVerifier;
    private ChangeEmailUserHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->uniqueEmailVerifier = $this->createMock(UniqueEmailVerifierInterface::class);

        $this->handler = new ChangeEmailUserHandler(
            $this->userRepository,
            $this->uniqueEmailVerifier
        );
    }

    public function testSuccessfulEmailChange(): void
    {
        $userId = new UserId();
        $newEmail = Email::fromString('newemail@example.com');
        $command = new ChangeEmailUser($userId, $newEmail);

        $user = User::create(
            Email::fromString('oldemail@example.com'),
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

        $this->uniqueEmailVerifier
            ->expects($this->once())
            ->method('verify')
            ->with($newEmail);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $result = ($this->handler)($command);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('newemail@example.com', $result->email->value);
    }

    public function testThrowsExceptionWhenUserNotFound(): void
    {
        $userId = new UserId();
        $newEmail = Email::fromString('newemail@example.com');
        $command = new ChangeEmailUser($userId, $newEmail);

        $this->userRepository
            ->expects($this->once())
            ->method('byId')
            ->with($userId)
            ->willThrowException(UserNotFound::withId($userId));

        $this->expectException(UserNotFound::class);

        ($this->handler)($command);
    }

    public function testThrowsExceptionWhenEmailAlreadyExists(): void
    {
        $userId = new UserId();
        $newEmail = Email::fromString('existing@example.com');
        $command = new ChangeEmailUser($userId, $newEmail);

        $user = User::create(
            Email::fromString('oldemail@example.com'),
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

        $this->uniqueEmailVerifier
            ->expects($this->once())
            ->method('verify')
            ->with($newEmail)
            ->willThrowException(EmailAlreadyUsed::withEmail($newEmail));

        $this->expectException(EmailAlreadyUsed::class);

        ($this->handler)($command);
    }
}
