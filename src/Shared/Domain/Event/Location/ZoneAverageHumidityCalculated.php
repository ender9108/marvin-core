<?php

namespace Marvin\Shared\Domain\Event\Location;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZoneAverageHumidityCalculated extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneName,
        public float $averageHumidity,
        public float $targetHumidity,
        public int $activeSensorsCount,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_name' => $this->zoneName,
            'average_humidity' => $this->averageHumidity,
            'target_humidity' => $this->targetHumidity,
            'active_sensors_count' => $this->activeSensorsCount,
            'occurred_at' => $this->occurredOn->format('c'),
        ];
    }
}
