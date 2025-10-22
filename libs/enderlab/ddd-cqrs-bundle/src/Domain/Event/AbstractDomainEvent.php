<?php

namespace EnderLab\DddCqrsBundle\Domain\Event;

use DateTimeImmutable;

abstract readonly class AbstractDomainEvent implements DomainEventInterface
{
    public DateTimeImmutable $occurredOn;

    public function __construct()
    {
        $this->occurredOn = new DateTimeImmutable();
    }
}
