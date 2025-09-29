<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer;

use Marvin\Shared\Domain\ValueObject\DatetimeValueObjectInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

final readonly class DatetimeValueObjectTransformer implements TransformCallableInterface
{
    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        if ($value instanceof DatetimeValueObjectInterface) {
            return $value->value;
        }

        return null;
    }
}
