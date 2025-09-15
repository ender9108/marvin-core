<?php
namespace Marvin\Security\Presentation\Api\Transformer;

use DateTimeImmutable;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Presentation\Api\Exception\TransformerException;

class ValueObjectTransformer
{
    public static function transformRolesToArray(mixed $value, object $source): array
    {
        if (!$value instanceof Roles) {
            throw new TransformerException('ValueObjectTransformer::transformRoles value must be an instance of Roles');
        }

        return $value->toArray();
    }

    public static function transformVoStringableToString(mixed $value, object $source): string
    {
        return (string) $value;
    }

    public static function transformVoDatetimeToDatetimeImmutable(mixed $value, object $source): ?DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);
    }
}
