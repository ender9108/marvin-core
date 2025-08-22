<?php

namespace EnderLab\EventSourcingBundle\Domain;

use EnderLab\DddCqrsBundle\Domain\Aggregate\AggregateRoot;

abstract class AggregateRootEventSourced extends AggregateRoot
{
    private array $uncommittedEvents = [];
    private int $version = 0;

    public function getUncommittedEvents(): array
    {
        return $this->uncommittedEvents;
    }

    protected function recordEvent(object $event): void
    {
        $this->applyEvent($event);
        $this->uncommittedEvents[] = $event;
    }

    public function clearUncommittedEvents(): void
    {
        $this->uncommittedEvents = [];
    }

    abstract protected function applyEvent(object $event): void;

    public function getVersion(): int
    {
        return $this->version;
    }

    protected function incrementVersion(): void
    {
        $this->version++;
    }
}
