<?php

namespace Marvin\Security\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Security\Domain\ValueObject\Identity\LoginAttemptId;

readonly class LoginAttempt
{
    public LoginAttemptId $id;

    private function __construct(
        public User $user,
        public DateTimeInterface $createdAt = new DateTimeImmutable()
    ) {
        $this->id = new LoginAttemptId();
    }

    public static function create(User $user): self
    {
        return new self($user);
    }
}
