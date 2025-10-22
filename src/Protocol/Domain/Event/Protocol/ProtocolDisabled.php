<?php

namespace Marvin\Protocol\Domain\Event\Protocol;

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

    public function getEventName(): string
    {
        return '$.protocol.protocol.disabled';
    }
}
