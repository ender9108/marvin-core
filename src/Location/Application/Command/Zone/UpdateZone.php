<?php

namespace Marvin\Location\Application\Command\Zone;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\TargetPowerConsumption;
use Marvin\Location\Domain\ValueObject\TargetTemperature;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;

final readonly class UpdateZone implements SyncCommandInterface
{
    public function __construct(
        public ZoneId $zoneId,
        public ?Label $label = null,
        public ?SurfaceArea $surfaceArea = null,
        public ?Orientation $orientation = null,
        public ?TargetTemperature $targetTemperature = null,
        public ?TargetPowerConsumption $targetPowerConsumption = null,
        public ?string $icon = null,
        public ?HexaColor $color = null,
    ) {
    }
}
