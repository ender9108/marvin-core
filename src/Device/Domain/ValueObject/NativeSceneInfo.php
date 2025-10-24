<?php

namespace Marvin\Device\Domain\ValueObject;

use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\Shared\Domain\ValueObject\ProtocolType;

final readonly class NativeSceneInfo
{
    public function __construct(
        public ProtocolType $protocolType,
        public ?ProtocolId $protocolId = null,
        public bool $isSupported = false,
        public ?string $nativeSceneId = null, // ID de la scène native
        public ?string $nativeSceneName = null, // Nom de la scène native
        public ?string $nativeGroupId = null, // Pour Zigbee, scènes liées aux groupes
        public ?Metadata $metadata = null
    ) {
    }

    public static function notSupported(ProtocolType $protocolType): self
    {
        return new self(
            protocolType: $protocolType,
            protocolId: null,
            isSupported: false
        );
    }

    public static function create(
        ProtocolType $protocolType,
        bool $isSupported,
        ?ProtocolId $protocolId = null,
        ?string $nativeSceneId = null,
        ?string $nativeSceneName = null,
        ?string $nativeGroupId = null,
        ?Metadata $metadata = null
    ): self {
        return new self(
            protocolType: $protocolType,
            protocolId: $protocolId,
            isSupported: $isSupported,
            nativeSceneId: $nativeSceneId,
            nativeSceneName: $nativeSceneName,
            nativeGroupId: $nativeGroupId,
            metadata: $metadata
        );
    }

    public function toArray(): array
    {
        return [
            'protocol_type' => $this->protocolType,
            'protocol_id' => $this->protocolId,
            'is_supported' => $this->isSupported,
            'native_scene_id' => $this->nativeSceneId,
            'native_scene_name' => $this->nativeSceneName,
            'native_group_id' => $this->nativeGroupId,
            'metadata' => $this->metadata->toArray(),
        ];
    }
}
