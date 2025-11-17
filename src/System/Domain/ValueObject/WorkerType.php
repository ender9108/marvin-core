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

namespace Marvin\System\Domain\ValueObject;

enum WorkerType: string
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
}
