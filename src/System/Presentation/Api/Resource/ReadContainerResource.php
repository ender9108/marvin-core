<?php

namespace Marvin\System\Presentation\Api\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer\EnumTransformer;
use Marvin\System\Domain\Model\Container;
use Marvin\System\Presentation\Api\State\Processor\RestartContainerProcessor;
use Marvin\System\Presentation\Api\State\Processor\StartContainerProcessor;
use Marvin\System\Presentation\Api\State\Processor\StopContainerProcessor;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: Container::class)]
#[ApiResource(
    shortName: 'container',
    operations: [
        new Get(security: 'is_granted("ROLE_ADMIN")'),
        new GetCollection(security: 'is_granted("ROLE_ADMIN")'),
        new Post(
            uriTemplate: '/containers/{id}/start',
            security: 'is_granted("ROLE_ADMIN")',
            processor: StartContainerProcessor::class,
        ),
        new Post(
            uriTemplate: '/containers/{id}/restart',
            security: 'is_granted("ROLE_ADMIN")',
            processor: RestartContainerProcessor::class,
        ),
        new Post(
            uriTemplate: '/containers/{id}/stop',
            security: 'is_granted("ROLE_ADMIN")',
            processor: StopContainerProcessor::class,
        )
    ],
    routePrefix: '/system',
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: Container::class),
)]
class ReadContainerResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    #[ApiProperty(writable: false)]
    public string $serviceLabel;

    #[ApiProperty(writable: false)]
    #[Map(transform: EnumTransformer::class)]
    public string $type;

    #[ApiProperty(writable: false)]
    #[Map(source: 'status', transform: EnumTransformer::class)]
    public string $uptime;

    #[ApiProperty(writable: false)]
    public string $containerId;

    #[ApiProperty(writable: false)]
    public string $containerLabel;
}
