<?php
namespace App\Domotic\Application\Command;

use DateTimeImmutable;

final readonly class DomoticEvent
{
    public function __construct(
        public string $protocol,            // ex: "zigbee", "matter", "zwave"
        public string $device,              // identifiant unique du device (ex: "ikea_motion_1")
        public string $eventType,           // type d’événement (ex: "presence", "temperature", "state_changed")
        public array  $payload = [],        // données brutes associées
        public DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {}
}
