<?php

namespace Marvin\Device\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Override;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Marvin\Device\Domain\ValueObject\NativeSceneInfo;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\Shared\Domain\ValueObject\ProtocolType;

final class NativeSceneInfoType extends JsonType
{
    public const string NAME = 'native_scene_info';

    #[Override]
    public function getName(): string
    {
        return self::NAME;
    }

    #[Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return json_encode($value->toArray());
    }

    #[Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?NativeSceneInfo
    {
        if ($value === null) {
            return null;
        }

        $values = json_decode($value, true);

        return NativeSceneInfo::create(
            ProtocolType::fromString($values['protocol_type']),
            (bool) $values['is_supported'],
            null !== $values['protocol_id'] ? ProtocolId::fromString($values['protocol_id']) : null,
            $values['native_scene_id'] ?? null,
            $values['native_scene_name'] ?? null,
            $values['native_group_id'] ?? null,
            null !== $values['metadata'] ? Metadata::fromArray($values['metadata']) : null,
        );
    }
}
