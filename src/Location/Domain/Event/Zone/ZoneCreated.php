<?php

namespace Marvin\Location\Domain\Event\Zone;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

final readonly class ZoneCreated implements DomainEventInterface
{
    public function __construct(
        public string $zoneId,
        public string $label,
        public string $type,
        public ?string $parentZoneId,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {}

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function getEventName(): string
    {
        return 'location.zone.created';
    }
}
