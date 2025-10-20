<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

enum WorkerType: string implements ValueObjectInterface
{
    case CONSUMER = 'consumer';
    case PROTOCOL = 'protocol';
    case CRON = 'cron';
    case MONITOR = 'monitor';
    case UNKNOWN = 'unknown';

    public static function fromString(string $value): self
    {
        return match ($value) {
            'consumer' => self::CONSUMER,
            'protocol' => self::PROTOCOL,
            'cron' => self::CRON,
            'monitor' => self::MONITOR,
            default => self::UNKNOWN,
        };
    }

    public function isConsumer(): bool
    {
        return $this === self::CONSUMER;
    }

    public function isProtocol(): bool
    {
        return $this === self::PROTOCOL;
    }

    public function isCron(): bool
    {
        return $this === self::CRON;
    }

    public function isMonitor(): bool
    {
        return $this === self::MONITOR;
    }

    public function isUnknown(): bool
    {
        return $this === self::UNKNOWN;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
