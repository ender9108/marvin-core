<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

enum ActionStatus: string implements ValueObjectInterface
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case TIMEOUT = 'timeout';

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

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
