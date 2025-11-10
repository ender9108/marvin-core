<?php

namespace Marvin\Protocol\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

final readonly class ProtocolDisabled extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(
        public string $protocolId,
        public string $protocolName,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'protocol_id' => $this->protocolId,
            'protocol_name' => $this->protocolName,
        ];
    }
}
