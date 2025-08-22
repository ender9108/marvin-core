<?php

namespace EnderLab\DddCqrsBundle\Domain\Aggregate;

use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\MappedSuperclass]
abstract class AggregateRoot
{
    #[ORM\Column(type: 'string', unique: true)]
    protected ?string $aggregateId = null;

    /** @var DomainEventInterface[] */
    private array $recordedEvents = [];

    public function __construct() {
        $this->aggregateId = new UuidV4();
    }

    public function getAggregateId(): ?string
    {
        return $this->aggregateId;
    }

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
