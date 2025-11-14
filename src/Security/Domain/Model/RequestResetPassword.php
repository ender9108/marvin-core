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

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Security\Domain\ValueObject\ExpiresAt;
use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordId;

class RequestResetPassword
{
    public private(set) ExpiresAt $expiresAt;

    public private(set) bool $used = false;

    /**
     * @throws DateMalformedStringException
     */
    public function __construct(
        private(set) string $token,
        private(set) User $user,
        public readonly DateTimeInterface $createdAt = new DateTimeImmutable(),
        private(set) RequestResetPasswordId $id = new RequestResetPasswordId(),
    ) {
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
