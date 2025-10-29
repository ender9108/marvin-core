<?php

namespace Marvin\Shared\Domain\Event\Location;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZoneTemperatureUpdated extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneName,
        public float $temperature,
        public string $sensorDeviceId,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_name' => $this->zoneName,
            'temperature' => $this->temperature,
            'sensor_device_id' => $this->sensorDeviceId,
            'occurred_at' => $this->occurredOn->format('c'),
        ];
    }
}
