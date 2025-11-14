<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsBundle\Application\Event;

use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

interface DomainEventBusInterface
{
    public function dispatch(DomainEventInterface $event): void;
}
