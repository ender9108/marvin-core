<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer;

use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

final readonly class SubResourceTransformer implements TransformCallableInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        return match (true) {
            default => null,
        };
    }
}
