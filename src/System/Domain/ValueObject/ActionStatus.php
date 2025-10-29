<?php

namespace Marvin\System\Domain\ValueObject;

enum ActionStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case TIMEOUT = 'timeout';

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isSuccess(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isFailure(): bool
    {
        return $this === self::FAILED || $this === self::TIMEOUT;
    }
}
