<?php

namespace Marvin\Shared\Domain\Event\Location;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZoneAverageTemperatureCalculated extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneName,
        public float $averageTemperature,
        public float $targetTemperature,
        public int $activeSensorsCount,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_name' => $this->zoneName,
            'average_temperature' => $this->averageTemperature,
            'target_temperature' => $this->targetTemperature,
            'active_sensors_count' => $this->activeSensorsCount,
            'occurred_at' => $this->occurredOn->format('c'),
        ];
    }
}
