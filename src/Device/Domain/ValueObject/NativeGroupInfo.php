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
 * Contains information about a native group created in a protocol
 * (e.g., Zigbee group, Z-Wave association, Matter group)
 */
final readonly class NativeGroupInfo implements Stringable
{
    use ValueObjectTrait;

    private function __construct(
        public string $nativeGroupId,
        public string $protocolId,
        public ?string $friendlyName = null,
        public ?array $metadata = null
    ) {
    }

    public static function create(
        string $nativeGroupId,
        string $protocolId,
        ?string $friendlyName = null,
        ?array $metadata = null
    ): self {
        return new self($nativeGroupId, $protocolId, $friendlyName, $metadata);
    }

    public function toArray(): array
    {
        return [
            'native_group_id' => $this->nativeGroupId,
            'protocol_id' => $this->protocolId,
            'friendly_name' => $this->friendlyName,
            'metadata' => $this->metadata,
        ];
    }

    public function __toString(): string
    {
        return (string) json_encode($this->toArray());
    }
}
