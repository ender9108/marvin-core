<?php

namespace Marvin\Protocol\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ProtocolRegistered extends AbstractDomainEvent
{
    public function __construct(
        public string $protocolId,
        public string $name,
        public string $transportType,
        public array $configuration,
        public string $preferredExecutionMode,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'protocol_id' => $this->protocolId,
            'name' => $this->name,
            'transport_type' => $this->transportType,
            'configuration' => $this->configuration,
            'preferred_execution_mode' => $this->preferredExecutionMode,
        ];
    }
}
