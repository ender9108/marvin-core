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

use Marvin\Security\Application\Command\User\UserLoginAttempt;
use Marvin\Security\Application\CommandHandler\User\UserLoginAttemptHandler;
use Marvin\Security\Domain\Model\LoginAttempt;
use Marvin\Security\Domain\Repository\LoginAttemptRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Email;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UserLoginAttemptHandlerTest extends TestCase
{
    private LoginAttemptRepositoryInterface|MockObject $loginAttemptRepository;
    private UserLoginAttemptHandler $handler;

    protected function setUp(): void
    {
        $this->loginAttemptRepository = $this->createMock(LoginAttemptRepositoryInterface::class);

        $this->handler = new UserLoginAttemptHandler($this->loginAttemptRepository);
    }

    public function testSuccessfulLoginAttemptRecording(): void
    {
        $email = Email::fromString('test@example.com');
        $ipAddress = '192.168.1.100';
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $success = true;

        $command = new UserLoginAttempt(
            $email,
            $ipAddress,
            $userAgent,
            $success
        );

        $this->loginAttemptRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (LoginAttempt $attempt) use ($email, $ipAddress, $success) {
                return $attempt->email->value === $email->value
                    && $attempt->ipAddress === $ipAddress
                    && $attempt->success === $success;
            }));

        ($this->handler)($command);
    }

    public function testFailedLoginAttemptRecording(): void
    {
        $email = Email::fromString('test@example.com');
        $ipAddress = '192.168.1.100';
        $userAgent = 'Mozilla/5.0';
        $success = false;

        $command = new UserLoginAttempt(
            $email,
            $ipAddress,
            $userAgent,
            $success
        );

        $this->loginAttemptRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(LoginAttempt::class));

        ($this->handler)($command);
    }
}
