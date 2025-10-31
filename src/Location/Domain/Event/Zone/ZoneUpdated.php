<?php

namespace Marvin\Location\Domain\Event\Zone;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZoneUpdated extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneName,
        public float $surfaceArea,
        public string $orientation,
        public ?float $targetTemperature = null,
        public ?float $targetPowerConsumption = null,
        public ?float $targetHumidity = null,
        public ?array $metadata = null,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_name' => $this->zoneName,
            'surface_area' => $this->surfaceArea,
            'orientation' => $this->orientation,
            'target_temperature' => $this->targetTemperature,
            'target_power_consumption' => $this->targetPowerConsumption,
            'target_humidity' => $this->targetHumidity,
            'occurred_at' => $this->occurredOn->format('c'),
            'metadata' => $this->metadata,
        ];
    }
}
