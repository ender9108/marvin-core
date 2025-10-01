<?php

namespace Marvin\System\Presentation\Api\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use DateTimeInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer\DatetimeValueObjectTransformer;
use Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer\SubCollectionResourceTransformer;
use Marvin\System\Domain\Model\Docker;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: Docker::class)]
#[ApiResource(
    shortName: 'docker',
    operations: [
        new Get(security: 'is_granted("ROLE_SUPER_ADMIN")'),
        new GetCollection(security: 'is_granted("ROLE_SUPER_ADMIN")'),
    ],
    routePrefix: '/system',
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: Docker::class),
)]
class ReadDockerResource
{
    #[ApiProperty(identifier: true)]
    public string $id;

    #[Map(transform: SubCollectionResourceTransformer::class)]
    public array $commands = [];

    public string $containerId;

    public string $containerName;

    public ?string $containerImage = null;

    public ?string $containerService = null;

    public ?string $containerState = null;

    public ?string $containerStatus = null;

    public ?string $containerProject = null;

    public array $definition = [];

    #[Map(transform: DatetimeValueObjectTransformer::class)]
    public DateTimeInterface $createdAt;

    #[Map(transform: DatetimeValueObjectTransformer::class)]
    public DateTimeInterface $updatedAt;
}
