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

namespace Marvin\Device\Application\Command\Scene;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * Command to capture and store current state of devices in a scene
 *
 * This captures the current state of all devices in the scene
 * and stores it for later restoration via ActivateScene
 */
final readonly class StoreSceneCurrentState implements SyncCommandInterface
{
    /**
     * @param DeviceId[]|null $deviceIds Optional list of specific devices to capture (if null, captures all scene devices)
     */
    public function __construct(
        public DeviceId $sceneId,
        public ?array $deviceIds = null,
    ) {
    }
}
