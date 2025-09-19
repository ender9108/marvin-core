<?php
namespace EnderLab\DddCqrsBundle\Application\Event;

use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

interface DomainEventHandlerInterface
{
    public static function supports(DomainEventInterface $event): bool;
}
