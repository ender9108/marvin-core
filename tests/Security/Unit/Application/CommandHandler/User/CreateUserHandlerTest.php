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

use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Application\CommandHandler\User\CreateUserHandler;
use Marvin\Security\Domain\Exception\EmailAlreadyUsed;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Marvin\Security\Domain\Service\UniqueEmailVerifierInterface;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Domain\ValueObject\Timezone;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateUserHandlerTest extends TestCase
{
    private UserRepositoryInterface|MockObject $userRepository;
    private PasswordHasherInterface|MockObject $passwordHasher;
    private UniqueEmailVerifierInterface|MockObject $uniqueEmailVerifier;
    private CreateUserHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(PasswordHasherInterface::class);
        $this->uniqueEmailVerifier = $this->createMock(UniqueEmailVerifierInterface::class);

        $this->handler = new CreateUserHandler(
            $this->userRepository,
            $this->passwordHasher,
            $this->uniqueEmailVerifier
        );
    }

    public function testSuccessfulUserCreation(): void
    {
        $command = new CreateUser(
            Email::fromString('test@example.com'),
            Firstname::fromString('John'),
            Lastname::fromString('Doe'),
            Roles::user(),
            Locale::fromString('en'),
            Theme::fromString('light'),
            Timezone::fromString('UTC'),
            'SecurePassword123!'
        );

        $this->uniqueEmailVerifier
            ->expects($this->once())
            ->method('verify')
            ->with($this->equalTo($command->email));

        $this->passwordHasher
            ->expects($this->once())
            ->method('hash')
            ->with('SecurePassword123!')
            ->willReturn('hashed_password');

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $user = ($this->handler)($command);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example.com', $user->email->value);
        $this->assertEquals('John', $user->firstname->value);
        $this->assertEquals('Doe', $user->lastname->value);
    }

    public function testThrowsExceptionWhenEmailAlreadyExists(): void
    {
        $command = new CreateUser(
            Email::fromString('existing@example.com'),
            Firstname::fromString('John'),
            Lastname::fromString('Doe'),
            Roles::user(),
            Locale::fromString('en'),
            Theme::fromString('light'),
            Timezone::fromString('UTC'),
            'SecurePassword123!'
        );

        $this->uniqueEmailVerifier
            ->expects($this->once())
            ->method('verify')
            ->with($this->equalTo($command->email))
            ->willThrowException(EmailAlreadyUsed::withEmail($command->email));

        $this->expectException(EmailAlreadyUsed::class);

        ($this->handler)($command);
    }
}
