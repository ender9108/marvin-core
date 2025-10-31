<?php

namespace Marvin\Location\Application\EventHandler;

use Marvin\Location\Domain\Event\Zone\ZoneDeleted;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ZoneDeletedHandler
{
    public function __invoke(ZoneDeleted $event): void
    {
        dump('Zone deleted: ' . $event->zoneId);
    }
}
