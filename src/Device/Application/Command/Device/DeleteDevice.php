<?php

namespace Marvin\Device\Application\Command\Device;

use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class DeleteDevice
{
    public function __construct(
        public DeviceId $deviceId
    ) {
    }
}
