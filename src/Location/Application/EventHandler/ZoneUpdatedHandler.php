<?php

namespace Marvin\Location\Application\EventHandler;

use Marvin\Location\Domain\Event\Zone\ZoneCreated;
use Marvin\Location\Domain\Event\Zone\ZoneUpdated;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ZoneUpdatedHandler
{
    public function __invoke(ZoneUpdated $event): void
    {
        dump('Zone updated: ' . $event->zoneId);
    }
}
