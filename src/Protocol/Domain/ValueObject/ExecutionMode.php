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

namespace Marvin\Protocol\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum ExecutionMode: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    case CORRELATION_ID = 'correlation_id';
    case DEVICE_LOCK = 'device_lock';
    case FIRE_AND_FORGET = 'fire_and_forget';

    public function isCorrelationId(): bool
    {
        return $this === self::CORRELATION_ID;
    }

    public function isDeviceLock(): bool
    {
        return $this === self::DEVICE_LOCK;
    }

    public function isFireAndForget(): bool
    {
        return $this === self::FIRE_AND_FORGET;
    }

    public function isSynchronous(): bool
    {
        return match ($this) {
            self::CORRELATION_ID, self::DEVICE_LOCK => true,
            self::FIRE_AND_FORGET => false,
        };
    }

    public function requiresLocking(): bool
    {
        return $this === self::DEVICE_LOCK;
    }

    public function requiresCorrelation(): bool
    {
        return $this === self::CORRELATION_ID;
    }
}
