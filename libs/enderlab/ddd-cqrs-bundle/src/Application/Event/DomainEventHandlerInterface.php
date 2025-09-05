<?php
namespace EnderLab\DddCqrsBundle\Application\Event;

use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

interface DomainEventHandlerInterface
{
    public function __invoke(DomainEventInterface $event): void;

    public static function supports(DomainEventInterface $event): bool;
}
