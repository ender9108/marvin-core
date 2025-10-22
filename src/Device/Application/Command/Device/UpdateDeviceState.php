<?php

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class UpdateDeviceState implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $deviceId,
        public string $capabilityName,
        public mixed $value,
        public ?string $unit = null
    ) {}
}
