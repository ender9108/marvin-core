<?php

namespace Marvin\System\Domain\ValueObject;

enum ContainerType: string
{
    case PROTOCOL = 'protocol';
    case DATABASE = 'database';
    case BROKER = 'broker';
    case MONITORING = 'monitoring';
    case MAILER = 'mailer';

    public function isProtocol(): bool
    {
        return $this === self::PROTOCOL;
    }

    public function isDatabase(): bool
    {
        return $this === self::DATABASE;
    }

    public function isBroker(): bool
    {
        return $this === self::BROKER;
    }

    public function isMonitoring(): bool
    {
        return $this === self::MONITORING;
    }

    public function isMailer(): bool
    {
        return $this === self::MAILER;
    }
}
