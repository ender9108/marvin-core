<?php

namespace Marvin\Shared\Domain\Event\Location;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZoneOccupancyChanged extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneName,
        public bool $isOccupied,
        public string $triggeredByDeviceId,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_name' => $this->zoneName,
            'is_occupied' => $this->isOccupied,
            'triggered_by_device_id' => $this->triggeredByDeviceId,
        ];
    }
}
