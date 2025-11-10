<?php

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
