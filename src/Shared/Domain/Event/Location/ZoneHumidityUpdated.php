<?php

namespace Marvin\Shared\Domain\Event\Location;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZoneHumidityUpdated extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneName,
        public float $humidity,
        public string $sensorDeviceId
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_name' => $this->zoneName,
            'humidity' => $this->humidity,
            'sensor_device_id' => $this->sensorDeviceId,
            'occurred_at' => $this->occurredOn->format('c'),
        ];
    }
}
