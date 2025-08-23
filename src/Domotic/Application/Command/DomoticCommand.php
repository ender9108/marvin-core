<?php
namespace App\Domotic\Application\Command;

use DateTimeImmutable;

final readonly class DomoticCommand
{
    public function __construct(
        public string $protocol,                // ex: "zigbee", "matter", "zwave"
        public string $device,                  // identifiant unique du device ciblé
        public array $payload = [],             // charge utile (format adapté au protocole)
        public DateTimeImmutable $issuedAt = new DateTimeImmutable(),
    ) {}
}
