<?php

namespace Marvin\Protocol\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ProtocolStatusChanged extends AbstractDomainEvent
{
    public function __construct(
        public string $protocolId,
        public string $previousStatus,
        public string $newStatus,
        public ?string $errorMessage = null,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'protocol_id' => $this->protocolId,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'error_message' => $this->errorMessage,
        ];
    }
}
