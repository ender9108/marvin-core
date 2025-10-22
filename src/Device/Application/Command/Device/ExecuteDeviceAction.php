<?php

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class ExecuteDeviceAction implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $deviceId,
        public string $capabilityName,
        public string $action,
        public array $params = []
    ) {}
}
