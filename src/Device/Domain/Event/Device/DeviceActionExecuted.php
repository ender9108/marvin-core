<?php

namespace Marvin\Device\Domain\Event\Device;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class DeviceActionExecuted extends AbstractDomainEvent
{
    public function __construct(
        public string $deviceId,
        public string $capability,
        public string $action,
        public array $params
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'device_id' => $this->deviceId,
            'capability' => $this->capability,
            'action' => $this->action,
            'params' => $this->params,
        ];
    }
}
