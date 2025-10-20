<?php

namespace Marvin\Location\Application\Command\Zone;

use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\TargetPowerConsumption;
use Marvin\Location\Domain\ValueObject\TargetTemperature;
use Marvin\Location\Domain\ValueObject\ZoneType;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

final readonly class CreateZone implements SyncCommandInterface
{
    public function __construct(
        public Label $label,
        public ZoneType $type,
        public ?ZoneId $parentZoneId = null,
        public ?SurfaceArea $surfaceArea = null,
        public ?Orientation $orientation = null,
        public ?TargetTemperature $targetTemperature = null,
        public ?TargetPowerConsumption $targetPowerConsumption = null,
        public ?string $icon = null,
        public ?HexaColor $color = null,
        public ?Metadata $metadata = null,
    ) {}
}
