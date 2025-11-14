<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */
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
