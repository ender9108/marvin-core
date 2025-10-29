<?php

namespace Marvin\Device\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Override;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use Marvin\Device\Domain\ValueObject\Identity\DeviceCapabilityId;

final class DeviceCapabilityIdType extends GuidType
{
    public const string NAME = 'device_capability_id';

    #[Override]
    public function getName(): string
    {
        return self::NAME;
    }

    #[Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value?->toString();
    }

    #[Override]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DeviceCapabilityId
    {
        if ($value === null) {
            return null;
        }

        return DeviceCapabilityId::fromString($value);
    }

    #[Override]
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
