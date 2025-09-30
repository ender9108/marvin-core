<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer;

use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\Model\UserType;
use Marvin\Security\Presentation\Api\Resource\UserStatus\UserStatusResource;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Marvin\System\Domain\Model\Docker;
use Marvin\System\Domain\Model\DockerCommand;
use Marvin\System\Presentation\Api\Resource\ReadDockerCommandResource;
use Marvin\System\Presentation\Api\Resource\ReadDockerResource;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

final readonly class SubResourceTransformer implements TransformCallableInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
    )
    {
    }

    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        return match (true) {
            $value instanceof UserStatus => $this->objectMapper->map($value, UserStatusResource::class),
            $value instanceof UserType => $this->objectMapper->map($value, UserTypeResource::class),
            $value instanceof Docker => $this->objectMapper->map($value, ReadDockerResource::class),
            $value instanceof DockerCommand => $this->objectMapper->map($value, ReadDockerCommandResource::class),
            default => null,
        };
    }
}
