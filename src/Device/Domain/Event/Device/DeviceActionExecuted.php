<?php

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class DeviceActionExecuted extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public string $capabilityName,
        public string $action,
        public array $params
    ) {
        parent::__construct();
    }
}
