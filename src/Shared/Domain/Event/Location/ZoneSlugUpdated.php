<?php

namespace Marvin\Shared\Domain\Event\Location;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class ZoneSlugUpdated extends AbstractDomainEvent
{
    public function __construct(
        public string $zoneId,
        public string $zoneLabel,
        public string $zoneSlug,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'zone_id' => $this->zoneId,
            'zone_label' => $this->zoneLabel,
            'zone_slug' => $this->zoneSlug,
            'occurred_at' => $this->occurredOn->format('c'),
        ];
    }
}
