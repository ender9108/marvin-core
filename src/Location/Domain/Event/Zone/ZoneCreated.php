<?php

namespace Marvin\Location\Domain\Event\Zone;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZoneCreated extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneName,
        public string $zoneType,
        public ?string $parentId = null,
        public ?float $surface = null,
        public ?float $targetTemperature = null,
        public ?float $targetPowerConsumption = null,
        public ?float $targetHumidity = null,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_name' => $this->zoneName,
            'zone_type' => $this->zoneType,
            'parent_id' => $this->parentId,
            'surface' => $this->surface,
            'target_temperature' => $this->targetTemperature,
            'target_power_consumption' => $this->targetPowerConsumption,
            'target_humidity' => $this->targetHumidity,
            'occurred_at' => $this->occurredOn->format('c'),
        ];
    }
}
