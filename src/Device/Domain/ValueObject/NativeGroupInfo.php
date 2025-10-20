<?php

namespace Marvin\Device\Domain\ValueObject;

final readonly class NativeGroupInfo
{
    public function __construct(
        public string $protocolType, // 'zigbee', 'matter', 'thread', 'zwave', 'wifi'
        public ?string $protocolId, // ID du protocol dans Protocol Context
        public bool $isSupported, // Le protocole supporte-t-il les groupes natifs ?
        public ?string $nativeGroupId = null, // ID du groupe natif (ex: "1" pour Zigbee)
        public ?string $nativeGroupName = null, // Nom du groupe natif
        public ?array $metadata = null // Infos supplémentaires protocole-specific
    ) {}

    public static function notSupported(string $protocolType): self
    {
        return new self(
            protocolType: $protocolType,
            protocolId: null,
            isSupported: false
        );
    }

    public static function zigbeeGroup(
        string $protocolId,
        string $groupName,
        int $groupId,
        ?array $metadata = null
    ): self {
        return new self(
            protocolType: 'zigbee',
            protocolId: $protocolId,
            isSupported: true,
            nativeGroupId: (string) $groupId,
            nativeGroupName: $groupName,
            metadata: $metadata
        );
    }

    public static function matterGroup(
        string $protocolId,
        string $groupName,
        int $groupId,
        ?array $metadata = null
    ): self {
        return new self(
            protocolType: 'matter',
            protocolId: $protocolId,
            isSupported: true,
            nativeGroupId: (string) $groupId,
            nativeGroupName: $groupName,
            metadata: $metadata
        );
    }

    public static function threadGroup(
        string $protocolId,
        string $groupName,
        int $groupId,
        ?array $metadata = null
    ): self {
        return new self(
            protocolType: 'thread',
            protocolId: $protocolId,
            isSupported: true,
            nativeGroupId: (string) $groupId,
            nativeGroupName: $groupName,
            metadata: $metadata
        );
    }

    public function toArray(): array
    {
        return [
            'protocol_type' => $this->protocolType,
            'protocol_id' => $this->protocolId,
            'is_supported' => $this->isSupported,
            'native_group_id' => $this->nativeGroupId,
            'native_group_name' => $this->nativeGroupName,
            'metadata' => $this->metadata,
        ];
    }
}
