<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer;

use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;
use Traversable;

final readonly class SubCollectionResourceTransformer implements TransformCallableInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        if (is_iterable($value)) {
            $callable = new SubResourceTransformer($this->objectMapper);

            foreach ($value as $item) {
                $results[] = $callable($item, $source, $target);
            }
        }

        return $results ?? $value;
    }
}
