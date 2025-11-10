<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * Command to update a device's state
 *
 * Typically triggered by Protocol Context when receiving state updates from physical devices
 * This is an async command as it will trigger domain events for Automation/Telemetry
 */
final readonly class UpdateDeviceState implements CommandInterface
{
    /**
     * @param array<string, mixed> $newState New state values (capability => value)
     */
    public function __construct(
        public DeviceId $deviceId,
        public array $newState,
    ) {
    }
}
