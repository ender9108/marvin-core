<?php

namespace Marvin\System\Domain\ValueObject;

enum ContainerStatus: string
{
    case RUNNING = 'running';
    case STOPPED = 'stopped';
    case PAUSED = 'paused';
    case RESTARTING = 'restarting';
    case EXITED = 'exited';
    case UNKNOWN = 'unknown';

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
