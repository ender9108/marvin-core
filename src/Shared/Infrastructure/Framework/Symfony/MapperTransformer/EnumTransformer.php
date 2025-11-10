<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer;

use BackedEnum;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

final readonly class EnumTransformer implements TransformCallableInterface
{
    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return is_string($value) ? $value : null;
    }
}
