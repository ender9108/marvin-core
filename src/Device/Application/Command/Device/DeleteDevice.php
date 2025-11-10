<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * Command to delete a device
 */
final readonly class DeleteDevice implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $deviceId,
    ) {
    }
}
