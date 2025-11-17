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

enum ContainerType: string
{
    case PROTOCOL = 'protocol';
    case DATABASE = 'database';
    case WORKER = 'worker';
    case MONITORING = 'monitoring';
    case MAILER = 'mailer';
    case APPLICATION = 'application';

    public function isProtocol(): bool
    {
        return $this === self::PROTOCOL;
    }

    public function isDatabase(): bool
    {
        return $this === self::DATABASE;
    }

    public function isWorker(): bool
    {
        return $this === self::WORKER;
    }

    public function isMonitoring(): bool
    {
        return $this === self::MONITORING;
    }

    public function isMailer(): bool
    {
        return $this === self::MAILER;
    }

    public function isApplication(): bool
    {
        return $this === self::APPLICATION;
    }
}
