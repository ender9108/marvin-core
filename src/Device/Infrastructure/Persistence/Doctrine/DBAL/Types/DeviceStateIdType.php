<?php

namespace Marvin\Device\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Override;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use Marvin\Device\Domain\ValueObject\Identity\DeviceStateId;

final class DeviceStateIdType extends GuidType
{
    public const string NAME = 'device_state_id';

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
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DeviceStateId
    {
        if ($value === null) {
            return null;
        }

        return DeviceStateId::fromString($value);
    }
}
