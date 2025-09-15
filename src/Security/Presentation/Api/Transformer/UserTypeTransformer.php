<?php

namespace Marvin\Security\Presentation\Api\Transformer;

use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

final readonly class UserTypeTransformer implements TransformCallableInterface
{
    public function __construct(private ObjectMapperInterface $objectMapper) {
    }

    public function __invoke(mixed $value, object $source, ?object $target): ?UserTypeResource
    {
        return $this->objectMapper->map($value, UserTypeResource::class);
    }
}
