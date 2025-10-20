<?php

namespace Marvin\Shared\Domain\Event;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

final readonly class DeviceStateChanged implements DomainEventInterface
{
    public function __construct(
        public string $zoneId,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {}

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function getEventName(): string
    {
        return '$.device.state.changed';
    }
}
