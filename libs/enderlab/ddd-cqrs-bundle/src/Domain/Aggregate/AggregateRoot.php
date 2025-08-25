<?php

namespace EnderLab\DddCqrsBundle\Domain\Aggregate;

use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\MappedSuperclass]
abstract class AggregateRoot
{

    /** @var DomainEventInterface[] */
    private array $recordedEvents = [];

    protected function recordThat(DomainEventInterface $event): void
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * @return DomainEventInterface[]
     */
    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }
}
