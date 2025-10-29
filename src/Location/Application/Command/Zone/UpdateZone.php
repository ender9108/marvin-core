<?php

namespace Marvin\Location\Application\Command\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final readonly class UpdateZone implements SyncCommandInterface
{
    public function __construct(
        public ZoneId             $zoneId,
        public ?ZoneName          $zoneName = null,
        public ?SurfaceArea       $surfaceArea = null,
        public ?Orientation       $orientation = null,
        public ?Temperature $targetTemperature = null,
        public ?PowerConsumption  $targetPowerConsumption = null,
        public ?string            $icon = null,
        public ?HexaColor         $color = null,
    ) {
    }
}
