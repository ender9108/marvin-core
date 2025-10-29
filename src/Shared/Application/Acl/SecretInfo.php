<?php

namespace Marvin\Shared\Application\Acl;

use DateMalformedStringException;

final readonly class SecretInfo
{
    public function __construct(
        public string $key,
        public string $scope,
        public string $category,
        public bool $autoRotate,
        public int $rotationIntervalDays,
        public ?\DateTimeImmutable $lastRotatedAt,
        public ?\DateTimeImmutable $expiresAt,
        public \DateTimeImmutable $createdAt,
    ) {
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return $this->expiresAt < new \DateTimeImmutable();
    }

    /**
     * @throws DateMalformedStringException
     */
    public function needsRotation(): bool
    {
        if (!$this->autoRotate || $this->lastRotatedAt === null) {
            return false;
        }

        $nextRotation = $this->lastRotatedAt->modify("+{$this->rotationIntervalDays} days");
        return $nextRotation <= new \DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'scope' => $this->scope,
            'category' => $this->category,
            'auto_rotate' => $this->autoRotate,
            'rotation_interval_days' => $this->rotationIntervalDays,
            'last_rotated_at' => $this->lastRotatedAt?->format('c'),
            'expires_at' => $this->expiresAt?->format('c'),
            'created_at' => $this->createdAt->format('c'),
        ];
    }
}
