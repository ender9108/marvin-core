<?php

namespace Marvin\Location\Application\Command\Zone;

use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final readonly class AddDeviceToZone
{
    public function __construct(
        public ZoneId $zoneId,
        public DeviceId $deviceId,
    ) {
    }
}
