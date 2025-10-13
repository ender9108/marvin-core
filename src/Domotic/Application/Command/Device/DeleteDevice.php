<?php

namespace Marvin\Domotic\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\DeviceId;

final readonly class DeleteDevice implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $id
    ) {
    }
}
