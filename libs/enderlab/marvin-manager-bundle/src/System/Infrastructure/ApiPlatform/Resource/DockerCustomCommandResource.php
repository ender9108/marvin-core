<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\MarvinManagerBundle\System\Domain\Model\DockerCustomCommand;

#[ApiResource(
    shortName: 'docker_custom_command',
    operations: [
        new GetCollection(),
        new Get(),
    ],
    routePrefix: 'system',
    normalizationContext: ['skip_null_values' => false,],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: DockerCustomCommand::class)
)]
#[ApiFilter(SearchFilter::class, properties: [
    'containerId' => 'exact',
    'containerName' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'containerId', 'containerName'])]
final class DockerCustomCommandResource implements ApiResourceInterface
{
    #[ApiProperty(identifier: true)]
    public ?string $id = null;

    public ?string $reference = null;

    public ?string $command = null;

    public ?DockerResource $docker = null;
}
