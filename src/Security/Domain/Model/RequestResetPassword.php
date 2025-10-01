<?php

namespace Marvin\Security\Domain\Model;

use DateMalformedStringException;
use DateTimeImmutable;
use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordIdType;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\ExpiresAt;

final readonly class RequestResetPassword
{
    public readonly RequestResetPasswordIdType $id;

    public private(set) ExpiresAt $expiresAt;

    public private(set) CreatedAt $createdAt;

    /**
     * @throws DateMalformedStringException
     */
    public function __construct(
        private(set) string $token,
        private(set) User $user,
    ) {
        $this->expiresAt = new ExpiresAt(new DateTimeImmutable()->modify('+1 day'));
        $this->createdAt = new CreatedAt(new DateTimeImmutable());
    }
}
