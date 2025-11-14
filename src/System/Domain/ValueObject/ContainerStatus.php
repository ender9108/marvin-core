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
