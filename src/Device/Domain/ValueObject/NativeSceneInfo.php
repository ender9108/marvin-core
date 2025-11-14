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

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

/**
 * Contains information about a native scene created in a protocol
 * (e.g., Zigbee scene, Z-Wave scene, Matter scene)
 */
final readonly class NativeSceneInfo implements Stringable
{
    use ValueObjectTrait;

    private function __construct(
        public string $nativeSceneId,
        public string $protocolId,
        public ?string $friendlyName = null,
        public ?string $groupId = null,
        public ?array $metadata = null
    ) {
    }

    public static function create(
        string $nativeSceneId,
        string $protocolId,
        ?string $friendlyName = null,
        ?string $groupId = null,
        ?array $metadata = null
    ): self {
        return new self($nativeSceneId, $protocolId, $friendlyName, $groupId, $metadata);
    }

    public function toArray(): array
    {
        return [
            'native_scene_id' => $this->nativeSceneId,
            'protocol_id' => $this->protocolId,
            'friendly_name' => $this->friendlyName,
            'group_id' => $this->groupId,
            'metadata' => $this->metadata,
        ];
    }

    public function __toString(): string
    {
        return (string) json_encode($this->toArray());
    }
}
