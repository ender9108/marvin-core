<?php

namespace Marvin\Device\Domain\ValueObject;

use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\Shared\Domain\ValueObject\ProtocolType;

final readonly class NativeGroupInfo
{
    public function __construct(
        public ProtocolType $protocolType, // 'zigbee', 'matter', 'thread', 'zwave', 'network'
        public bool $isSupported, // Le protocole supporte-t-il les groupes natifs ?
        public ?ProtocolId $protocolId = null, // ID du protocol dans Protocol Context
        public ?string $nativeGroupId = null, // ID du groupe natif (ex: "1" pour Zigbee)
        public ?string $nativeGroupName = null, // Nom du groupe natif
        public ?Metadata $metadata = null // Infos supplémentaires protocole-specific
    ) {
    }

    public static function notSupported(ProtocolType $protocolType): self
    {
        return new self(
            protocolType: $protocolType,
            isSupported: false,
            protocolId: null
        );
    }

    public static function create(
        ProtocolType $protocolType,
        bool $isSupported,
        ?ProtocolId $protocolId = null,
        ?string $nativeGroupId = null,
        ?string $nativeGroupName = null,
        ?Metadata $metadata = null
    ): self {
        return new self(
            protocolType: $protocolType,
            isSupported: $isSupported,
            protocolId: $protocolId,
            nativeGroupId: $nativeGroupId,
            nativeGroupName: $nativeGroupName,
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
            'metadata' => $this->metadata->toArray(),
        ];
    }
}
