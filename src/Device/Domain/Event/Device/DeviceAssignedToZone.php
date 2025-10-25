<?php

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class DeviceAssignedToZone extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public ?string $zoneId,
        public ?string $previousZoneId = null
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'device_id' => $this->deviceId,
            'zone_id' => $this->zoneId,
            'previous_zone_id' => $this->previousZoneId,
        ];
    }
}
