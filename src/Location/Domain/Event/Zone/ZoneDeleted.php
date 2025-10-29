<?php

namespace Marvin\Location\Domain\Event\Zone;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZoneDeleted extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneName,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_name' => $this->zoneName,
            'occurred_at' => $this->occurredOn->format('c'),
        ];
    }
}
