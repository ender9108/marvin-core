<?php

namespace Marvin\Location\Application\Command\Zone;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Location\Domain\ValueObject\Humidity;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class UpdateZoneMetricsFromDevice implements CommandInterface
{
    public function __construct(
        public DeviceId $deviceId,
        public ?Temperature $temperature = null,
        public ?Humidity $humidity = null,
        public ?PowerConsumption $powerWatts = null,
        public ?bool $motionDetected = null,
    ) {
    }
}
