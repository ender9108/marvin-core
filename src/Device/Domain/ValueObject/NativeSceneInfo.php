<?php

namespace Marvin\Device\Domain\ValueObject;

final readonly class NativeSceneInfo
{
    public function __construct(
        public string $protocolType,
        public ?string $protocolId,
        public bool $isSupported,
        public ?string $nativeSceneId = null, // ID de la scène native
        public ?string $nativeSceneName = null, // Nom de la scène native
        public ?string $nativeGroupId = null, // Pour Zigbee, scènes liées aux groupes
        public ?array $metadata = null
    ) {}

    public static function notSupported(string $protocolType): self
    {
        return new self(
            protocolType: $protocolType,
            protocolId: null,
            isSupported: false
        );
    }

    public static function zigbeeScene(
        string $protocolId,
        string $groupName,
        int $sceneId,
        string $sceneName,
        ?array $metadata = null
    ): self {
        return new self(
            protocolType: 'zigbee',
            protocolId: $protocolId,
            isSupported: true,
            nativeSceneId: (string) $sceneId,
            nativeSceneName: $sceneName,
            nativeGroupId: $groupName, // Zigbee scenes are linked to groups
            metadata: $metadata
        );
    }

    public static function matterScene(
        string $protocolId,
        int $sceneId,
        string $sceneName,
        ?array $metadata = null
    ): self {
        return new self(
            protocolType: 'matter',
            protocolId: $protocolId,
            isSupported: true,
            nativeSceneId: (string) $sceneId,
            nativeSceneName: $sceneName,
            metadata: $metadata
        );
    }

    public static function zwaveScene(
        string $protocolId,
        int $sceneId,
        string $sceneName,
        ?array $metadata = null
    ): self {
        return new self(
            protocolType: 'zwave',
            protocolId: $protocolId,
            isSupported: true,
            nativeSceneId: (string) $sceneId,
            nativeSceneName: $sceneName,
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
            'metadata' => $this->metadata,
        ];
    }
}
