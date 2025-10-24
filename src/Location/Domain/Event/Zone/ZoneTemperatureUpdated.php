<?php

namespace Marvin\Location\Domain\Event\Zone;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

final readonly class ZoneTemperatureUpdated implements DomainEventInterface
{
    public function __construct(
        public string $zoneId,
        public float $oldTemperature,
        public float $newTemperature,
        public DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function getEventName(): string
    {
        return 'location.zone.temperature_updated';
    }

    public function getTemperatureDelta(): float
    {
        return round($this->newTemperature - $this->oldTemperature, 1);
    }
}
