<?php

namespace EnderLab\LockableBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class AsLockableMessage
{
    public function __construct(
        public string $lockName,
        public bool $read = false,
        public bool $blocking = false,
        public float $ttl = 300,
    ) {

    }

    public function getLockName(): string
    {
        return $this->lockName;
    }

    public function isRead(): bool
    {
        return $this->read;
    }

    public function getTtl(): ?float
    {
        return $this->ttl;
    }

    public function isBlocking(): bool
    {
        return $this->blocking;
    }
}
