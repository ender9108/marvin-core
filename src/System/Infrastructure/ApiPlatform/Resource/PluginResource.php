<?php

namespace App\System\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\System\Domain\Model\Plugin;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'plugin',
    operations: [
        new GetCollection(),
        new Get(),
        new Patch(security: 'is_granted("ROLE_ADMIN")'),
    ],
    routePrefix: 'system',
    normalizationContext: ['skip_null_values' => false,],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: Plugin::class)
)]
#[ApiFilter(SearchFilter::class, properties: [
    'label' => 'partial',
    'reference' => 'exact',
    'status.id' => 'exact',
    'status.reference' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'label', 'reference'])]
final class PluginResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?string $id = null;

    #[ApiProperty(readable: true, writable: false)]
    public ?string $label = null;

    #[ApiProperty(readable: true, writable: false)]
    public ?string $description = null;

    #[ApiProperty(readable: true, writable: false)]
    public ?string $reference = null;

    #[ApiProperty(readable: true, writable: false)]
    public ?string $version = null;

    public array $metadata = [];

    #[Assert\NotNull]
    public ?PluginStatusResource $status = null;
}
