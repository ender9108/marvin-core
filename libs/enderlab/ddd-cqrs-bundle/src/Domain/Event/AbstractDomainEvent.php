<?php

namespace EnderLab\DddCqrsBundle\Domain\Event;

use DateTimeImmutable;

class AbstractDomainEvent implements DomainEventInterface
{
    private DateTimeImmutable $occurredOn;

    public function __construct()
    {
        $this->occurredOn = new DateTimeImmutable();
    }

    public function getOccurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
