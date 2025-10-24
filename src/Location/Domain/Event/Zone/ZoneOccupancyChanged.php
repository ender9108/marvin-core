<?php

namespace Marvin\Location\Domain\Event\Zone;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

final readonly class ZoneOccupancyChanged implements DomainEventInterface
{
    public function __construct(
        public string $zoneId,
        public bool $isOccupied,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function getEventName(): string
    {
        return 'location.zone.occupancy_changed';
    }
}
