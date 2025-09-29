<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Override;
use Throwable;

final class CreatedAtType extends DateTimeType
{
    public const string NAME = 'createdAt';

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?CreatedAt
    {
        $value = parent::convertToPHPValue($value, $platform);

        try {
            return new CreatedAt($value);
        } catch (Throwable $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    #[Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value instanceof CreatedAt) {
            $value = $value->value;
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    #[Override]
    public function getName(): string
    {
        return self::NAME;
    }
}
