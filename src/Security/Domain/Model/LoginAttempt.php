<?php
namespace Marvin\Security\Domain\Model;

use DateTimeImmutable;
use Marvin\Security\Domain\ValueObject\Identity\LoginAttemptId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;

readonly class LoginAttempt
{
    public LoginAttemptId $id;

    private function __construct(
        public User $user,
        public CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new LoginAttemptId();
    }

    public static function create(User $user): self
    {
        return new self($user);
    }
}
