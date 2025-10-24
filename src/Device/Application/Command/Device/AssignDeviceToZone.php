<?php

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final readonly class AssignDeviceToZone implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $deviceId,
        public ?ZoneId $zoneId = null // null => unassign
    ) {
    }
}
