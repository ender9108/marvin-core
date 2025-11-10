<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

/**
 * Command to assign a device to a zone
 */
final readonly class AssignDeviceToZone implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $deviceId,
        public ZoneId $zoneId,
    ) {
    }
}
