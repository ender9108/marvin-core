<?php

namespace Marvin\Location\Application\Command\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Humidity;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Metadata;

final readonly class CreateZone implements SyncCommandInterface
{
    public function __construct(
        public ZoneName $zoneName,
        public ZoneType $type,
        public ?ZoneId $parentZoneId = null,
        public ?SurfaceArea $surfaceArea = null,
        public ?Orientation $orientation = null,
        public ?Temperature $targetTemperature = null,
        public ?Humidity $targetHumidity = null,
        public ?PowerConsumption $targetPowerConsumption = null,
        public ?string $icon = null,
        public ?HexaColor $color = null,
        public ?Metadata $metadata = null,
    ) {
    }
}
