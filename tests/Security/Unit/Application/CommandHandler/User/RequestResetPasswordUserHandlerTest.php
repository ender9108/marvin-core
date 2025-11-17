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

use Marvin\Security\Application\Command\User\RequestResetPasswordUser;
use Marvin\Security\Application\CommandHandler\User\RequestResetPasswordUserHandler;
use Marvin\Security\Domain\Exception\RequestResetPasswordAlreadyExists;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Model\RequestResetPassword;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\RequestResetPasswordRepositoryInterface;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Shared\Application\Email\MailerInterface;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RequestResetPasswordUserHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private RequestResetPasswordRepositoryInterface|MockObject $requestResetPasswordRepository;
    private MailerInterface|MockObject $mailer;
    private RequestResetPasswordUserHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->requestResetPasswordRepository = $this->createMock(RequestResetPasswordRepositoryInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);

        $this->handler = new RequestResetPasswordUserHandler(
            $this->userRepository,
            $this->requestResetPasswordRepository,
            $this->mailer
        );
    }

    public function testSuccessfulPasswordResetRequest(): void
    {
        $email = Email::fromString('test@example.com');
        $command = new RequestResetPasswordUser($email);

        $user = User::create(
            $email,
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
            ->method('byEmail')
            ->with($email)
            ->willReturn($user);

        $this->requestResetPasswordRepository
            ->expects($this->once())
            ->method('checkIfRequestAlreadyExists')
            ->with($user)
            ->willReturn(false);

        $this->requestResetPasswordRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(RequestResetPassword::class));

        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->anything());

        $result = ($this->handler)($command);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('test@example.com', $result->email->value);
    }

    public function testThrowsExceptionWhenUserNotFound(): void
    {
        $email = Email::fromString('notfound@example.com');
        $command = new RequestResetPasswordUser($email);

        $this->userRepository
            ->expects($this->once())
            ->method('byEmail')
            ->with($email)
            ->willReturn(null);

        $this->expectException(UserNotFound::class);

        ($this->handler)($command);
    }

    public function testThrowsExceptionWhenRequestAlreadyExists(): void
    {
        $email = Email::fromString('test@example.com');
        $command = new RequestResetPasswordUser($email);

        $user = User::create(
            $email,
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
            ->method('byEmail')
            ->with($email)
            ->willReturn($user);

        $this->requestResetPasswordRepository
            ->expects($this->once())
            ->method('checkIfRequestAlreadyExists')
            ->with($user)
            ->willReturn(true);

        $this->expectException(RequestResetPasswordAlreadyExists::class);

        ($this->handler)($command);
    }
}
