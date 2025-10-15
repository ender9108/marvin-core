<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

enum WorkerStatus: string implements ValueObjectInterface
{
    case RUNNING = 'running';
    case STOPPED = 'stopped';
    case STARTING = 'starting';
    case FATAL = 'fatal';
    case BACKOFF = 'backoff';
    case UNKNOWN = 'unknown';

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function isRunning(): bool
    {
        return $this === self::RUNNING;
    }

    public function isStarting(): bool
    {
        return $this === self::STARTING;
    }

    public function isStopped(): bool
    {
        return $this === self::STOPPED;
    }

    public function isFailed(): bool
    {
        return $this === self::FATAL || $this === self::BACKOFF;
    }
}
