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
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\ExecutionStrategy;
use Marvin\Device\Domain\ValueObject\SceneStates;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

/**
 * Command to create a new scene (composite device with predefined states)
 *
 * Scenes restore predefined states for multiple devices
 */
final readonly class CreateScene implements SyncCommandInterface
{
    public function __construct(
        public Label $label,
        public ?SceneStates $sceneStates = null,
        public CompositeStrategy $compositeStrategy = CompositeStrategy::NATIVE_IF_AVAILABLE,
        public ExecutionStrategy $executionStrategy = ExecutionStrategy::SEQUENTIAL,
        public ?ZoneId $zoneId = null,
        public ?Description $description = null,
        public ?Metadata $metadata = null,
    ) {
    }
}
