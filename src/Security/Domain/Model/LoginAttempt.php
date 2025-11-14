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

namespace Marvin\Security\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Security\Domain\ValueObject\Identity\LoginAttemptId;

readonly class LoginAttempt
{
    private function __construct(
        public User $user,
        public DateTimeInterface $createdAt = new DateTimeImmutable(),
        public LoginAttemptId $id = new LoginAttemptId(),
    ) {
    }

    public static function create(User $user): self
    {
        return new self($user);
    }
}
