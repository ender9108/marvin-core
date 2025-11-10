<?php

namespace Marvin\Protocol\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ProtocolCommandSent extends AbstractDomainEvent
{
    public function __construct(
        public string $protocolId,
        public string $deviceId,
        public string $action,
        public array $parameters,
        public string $executionMode,
        public ?string $correlationId = null,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'protocol_id' => $this->protocolId,
            'device_id' => $this->deviceId,
            'action' => $this->action,
            'parameters' => $this->parameters,
            'execution_mode' => $this->executionMode,
            'correlation_id' => $this->correlationId,
        ];
    }
}
