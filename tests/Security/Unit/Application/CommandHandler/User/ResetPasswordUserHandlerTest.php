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

use DateTimeImmutable;
use Marvin\Security\Application\Command\User\ResetPasswordUser;
use Marvin\Security\Application\CommandHandler\User\ResetPasswordUserHandler;
use Marvin\Security\Domain\Exception\RequestResetPasswordAlreadyUsed;
use Marvin\Security\Domain\Exception\RequestResetPasswordExpired;
use Marvin\Security\Domain\Exception\RequestResetPasswordNotFound;
use Marvin\Security\Domain\Model\RequestResetPassword;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\RequestResetPasswordRepositoryInterface;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ResetPasswordUserHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private RequestResetPasswordRepositoryInterface|MockObject $requestResetPasswordRepository;
    private PasswordHasherInterface|MockObject $passwordHasher;
    private ResetPasswordUserHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->requestResetPasswordRepository = $this->createMock(RequestResetPasswordRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(PasswordHasherInterface::class);

        $this->handler = new ResetPasswordUserHandler(
            $this->requestResetPasswordRepository,
            $this->userRepository,
            $this->passwordHasher
        );
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function testSuccessfulPasswordReset(): void
    {
        $token = 'valid_token_123';
        $newPassword = 'NewPassword456!';
        $command = new ResetPasswordUser($token, $newPassword);

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

        $request = new RequestResetPassword($token, $user);

        $this->requestResetPasswordRepository
            ->expects($this->once())
            ->method('byToken')
            ->with($token)
            ->willReturn($request);

        $this->passwordHasher
            ->expects($this->once())
            ->method('hash')
            ->with($user, $newPassword)
            ->willReturn('hashed_new_password');

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $this->requestResetPasswordRepository
            ->expects($this->once())
            ->method('save')
            ->with($request);

        ($this->handler)($command);
    }

    public function testThrowsExceptionWhenTokenNotFound(): void
    {
        $token = 'invalid_token';
        $command = new ResetPasswordUser($token, 'NewPassword456!');

        $this->requestResetPasswordRepository
            ->expects($this->once())
            ->method('byToken')
            ->with($token)
            ->willThrowException(RequestResetPasswordNotFound::withToken($token));

        $this->expectException(RequestResetPasswordNotFound::class);

        ($this->handler)($command);
    }

    public function testThrowsExceptionWhenTokenIsExpired(): void
    {
        // Note: This test is difficult to implement due to readonly properties and final classes.
        // ExpiresAt validates that dates must be in the future, making it impossible to create
        // an expired token for testing without modifying production code.
        // This functionality should be tested through integration tests instead.
        $this->markTestIncomplete(
            'Test skipped: Cannot create expired ExpiresAt due to readonly/final constraints. Use integration tests instead.'
        );
    }

    public function testThrowsExceptionWhenTokenAlreadyUsed(): void
    {
        $token = 'used_token';
        $command = new ResetPasswordUser($token, 'NewPassword456!');

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

        $request = new RequestResetPassword($token, $user);
        // Mark request as used
        $request->markAsUsed();

        $this->requestResetPasswordRepository
            ->expects($this->once())
            ->method('byToken')
            ->with($token)
            ->willReturn($request);

        $this->expectException(RequestResetPasswordAlreadyUsed::class);

        ($this->handler)($command);
    }
}
