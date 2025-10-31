<?php

namespace Marvin\Location\Application\EventHandler;

use Marvin\Location\Domain\Event\Zone\ZoneCreated;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ZoneCreatedHandler
{
    public function __invoke(ZoneCreated $event): void
    {
        dump('Zone created: ' . $event->zoneId);
    }
}
