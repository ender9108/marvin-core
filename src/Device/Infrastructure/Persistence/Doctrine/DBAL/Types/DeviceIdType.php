<?php

namespace Marvin\Device\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use Marvin\Device\Domain\ValueObject\Identity\DeviceId;

final class DeviceIdType extends GuidType
{
    public const NAME = 'device_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value?->toString();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DeviceId
    {
        if ($value === null) {
            return null;
        }

        return DeviceId::fromString($value);
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
