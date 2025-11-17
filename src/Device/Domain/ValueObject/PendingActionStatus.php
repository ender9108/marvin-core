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

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

/**
 * PendingActionStatus - Status of a pending device action
 *
 * Tracks the lifecycle of asynchronous device actions:
 * - WAITING: Action sent, awaiting response
 * - COMPLETED: Response received, action successful
 * - FAILED: Action failed or error response
 * - TIMEOUT: No response within timeout period
 */
enum PendingActionStatus: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    /**
     * Action sent to device, awaiting response
     */
    case WAITING = 'waiting';

    /**
     * Response received, action completed successfully
     */
    case COMPLETED = 'completed';

    /**
     * Action failed or error response received
     */
    case FAILED = 'failed';

    /**
     * No response received within timeout period
     */
    case TIMEOUT = 'timeout';

    public function isWaiting(): bool
    {
        return $this === self::WAITING;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }

    public function isTimeout(): bool
    {
        return $this === self::TIMEOUT;
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::COMPLETED, self::FAILED, self::TIMEOUT => true,
            self::WAITING => false,
        };
    }
}
