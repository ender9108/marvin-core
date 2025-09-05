<?php

namespace EnderLab\DddCqrsBundle\Application\Event;

use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

trait DomainEventHandlerTrait
{
    public static function supports(DomainEventInterface $event): bool
    {
        return in_array($event::getRoutingKey(), self::$routingKeys);
    }
}
