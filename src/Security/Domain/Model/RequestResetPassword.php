<?php

namespace Marvin\Security\Domain\Model;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Security\Domain\ValueObject\ExpiresAt;
use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordId;

class RequestResetPassword
{
    public private(set) RequestResetPasswordId $id;

    public private(set) ExpiresAt $expiresAt;

    public private(set) bool $used = false;

    /**
     * @throws DateMalformedStringException
     */
    public function __construct(
        private(set) string $token,
        private(set) User $user,
        public readonly DateTimeInterface $createdAt = new DateTimeImmutable()
    ) {
        $this->id = new RequestResetPasswordId();
        $this->expiresAt = new ExpiresAt(new DateTimeImmutable()->modify('+1 day'));
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function markAsUsed(): void
    {
        $this->used = true;
    }
}
