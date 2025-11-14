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

namespace Marvin\Device\Domain\Event\Scene;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

/**
 * Domain Event: Scene states were updated
 *
 * Emitted when a scene's stored states are updated (e.g., via StoreSceneCurrentState)
 */
final readonly class SceneStatesUpdated extends AbstractDomainEvent
{
    public function __construct(
        public string $sceneId,
        public string $sceneLabel,
        public int $deviceCount,
    ) {
        parent::__construct();
    }

    public function getAggregateId(): string
    {
        return $this->sceneId;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'scene_id' => $this->sceneId,
            'scene_label' => $this->sceneLabel,
            'device_count' => $this->deviceCount,
        ];
    }
}
