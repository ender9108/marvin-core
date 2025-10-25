<?php

namespace Marvin\Protocol\Domain\Event\Protocol;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

final readonly class ProtocolDeleted extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(
        public string $protocolId,
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return '$.protocol.protocol.deleted';
    }

    public function toArray(): array
    {
        return [
            'protocol_id' => $this->protocolId,
        ];
    }
}
