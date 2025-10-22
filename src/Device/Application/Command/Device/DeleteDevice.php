<?php

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class DeleteDevice implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $deviceId
    ) {}
}
