<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\MapperTransformer;

use Symfony\Component\ObjectMapper\TransformCallableInterface;

final readonly class RolesTransformer implements TransformCallableInterface
{
    public function __invoke(mixed $value, object $source, ?object $target): array
    {
        if (is_array($value)) {
            return $value;
        }

        return $source->roles->toArray();
    }
}
