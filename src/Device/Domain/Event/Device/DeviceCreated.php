<?php

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class DeviceCreated extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public string $label,
        public string $type,
        public ?string $protocolId = null,
        public ?string $zoneId = null,
        public ?string $virtualDeviceType = null,
        public ?string $compositeType = null,
        public ?int $childCount = null,
    ) {
        parent::__construct();
    }
}
