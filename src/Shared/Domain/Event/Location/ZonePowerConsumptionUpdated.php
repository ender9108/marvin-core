<?php

namespace Marvin\Shared\Domain\Event\Location;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZonePowerConsumptionUpdated extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneName,
        public float $totalPowerConsumption,
        public int $devicesCount,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_name' => $this->zoneName,
            'total_power_consumption' => $this->totalPowerConsumption,
            'devices_count' => $this->devicesCount,
            'occurred_at' => $this->occurredOn->format('c'),
        ];
    }
}
