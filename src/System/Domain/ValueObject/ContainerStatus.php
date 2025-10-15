<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

enum ContainerStatus: string implements ValueObjectInterface
{
    case RUNNING = 'running';
    case STOPPED = 'stopped';
    case PAUSED = 'paused';
    case RESTARTING = 'restarting';
    case EXITED = 'exited';
    case UNKNOWN = 'unknown';

    public function equals(ValueObjectInterface $containerStatus): bool
    {
        return $containerStatus instanceof self && $this->value === $containerStatus->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function isRunning(): bool
    {
        return $this === self::RUNNING;
    }

    public function isStopped(): bool
    {
        return $this === self::STOPPED;
    }

    public function isExited(): bool
    {
        return $this === self::EXITED;
    }

    public function isPaused(): bool
    {
        return $this === self::PAUSED;
    }

    public function isRestarting(): bool
    {
        return $this === self::RESTARTING;
    }
}
