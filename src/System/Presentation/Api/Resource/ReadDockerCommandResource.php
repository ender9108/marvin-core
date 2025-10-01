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
use Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer\SubResourceTransformer;
use Marvin\System\Domain\Model\DockerCommand;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: DockerCommand::class)]
#[ApiResource(
    shortName: 'docker',
    operations: [
        new Get(security: 'is_granted("ROLE_SUPER_ADMIN")'),
        new GetCollection(security: 'is_granted("ROLE_SUPER_ADMIN")'),
    ],
    routePrefix: '/system',
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: DockerCommand::class),
)]
class ReadDockerCommandResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    #[ApiProperty(writable: false)]
    public string $reference;

    #[ApiProperty(writable: false)]
    public string $command;

    #[Map(transform: SubResourceTransformer::class)]
    #[ApiProperty(writable: false)]
    public ReadDockerResource $docker;

    #[Map(transform: DatetimeValueObjectTransformer::class)]
    #[ApiProperty(writable: false)]
    public DateTimeInterface $createdAt;

    #[ApiProperty(writable: false)]
    #[Map(transform: DatetimeValueObjectTransformer::class)]
    public DateTimeInterface $updatedAt;
}
