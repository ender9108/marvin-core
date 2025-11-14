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

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CapabilityAction;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * Command to execute an action on a device
 *
 * Can be used for:
 * - Physical devices (single action)
 * - Composite devices (group/scene with execution strategy)
 */
final readonly class ExecuteDeviceAction implements SyncCommandInterface
{
    /**
     * @param array<string, mixed> $parameters Action parameters (e.g., brightness value, color, etc.)
     */
    public function __construct(
        public DeviceId $deviceId,
        public Capability $capability,
        public CapabilityAction $action,
        public array $parameters = []
    ) {
    }
}
