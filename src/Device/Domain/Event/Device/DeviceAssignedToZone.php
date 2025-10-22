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
}
