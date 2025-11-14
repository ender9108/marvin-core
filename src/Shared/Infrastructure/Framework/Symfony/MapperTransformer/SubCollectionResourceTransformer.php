<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer;

use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

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
