<?php

namespace EnderLab\DddCqrsBundle\Domain\Event;

use DateTimeImmutable;

interface DomainEventInterface
{
    public function getOccurredOn(): DateTimeImmutable;
}
