<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

enum ContainerType: string implements ValueObjectInterface
{
    case PROTOCOL = 'protocol';
    case DATABASE = 'database';
    case BROKER = 'broker';
    case MONITORING = 'monitoring';
    case MAILER = 'mailer';

    public function equals(ValueObjectInterface $containerStatus): bool
    {
        return $containerStatus instanceof self && $this->value === $containerStatus->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

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
