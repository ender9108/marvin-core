<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\MapperTransformer;

use Symfony\Component\ObjectMapper\TransformCallableInterface;

final readonly class ArrayValueObjectTransformer implements TransformCallableInterface
{
    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        return $source->roles->toArray();
    }
}
