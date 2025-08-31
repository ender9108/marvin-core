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
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\MarvinManagerBundle\System\Domain\Model\Docker;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;

#[ApiResource(
    shortName: 'docker',
    operations: [
        new GetCollection(),
        new Get(),
    ],
    routePrefix: 'system',
    normalizationContext: ['skip_null_values' => false,],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: Docker::class)
)]
#[ApiFilter(SearchFilter::class, properties: [
    'containerId' => 'exact',
    'containerName' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'containerId', 'containerName'])]
final class DockerResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(identifier: true)]
    public ?string $id = null;

    public ?string $containerId = null;

    public ?string $containerName = null;

    public ?string $containerImage = null;

    public ?string $containerService = null;

    public ?string $containerState = null;

    public ?string $containerStatus = null;

    public ?string $containerProject = null;

    public array $definition = [];

    /** @var array <int, DockerCustomCommandResource> */
    public array $dockerCustomCommands = [];
}
