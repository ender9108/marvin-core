<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Domotic\Domain\Model\CapabilityComposition;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;

#[ApiResource(
    shortName: 'capability_composition',
    operations: [
        new GetCollection(),
        new Get(),
        new Put(),
        new Patch(),
        new Delete(),
    ],
    routePrefix: 'domotic',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: CapabilityComposition::class)
)]
#[ApiFilter(SearchFilter::class, properties: [
    'capability.label' => 'partial',
    'capability.reference' => 'exact',
    'capabilityActions.label' => 'partial',
    'capabilityActions.reference' => 'exact',
    'capabilityStates.label' => 'partial',
    'capabilityStates.reference' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id',
    'capability.label',
])]
final class CapabilityCompositionResource implements ApiResourceInterface
{
    use ResourceBlameableTrait;
    use ResourceTimestampableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?int $id = null;

    public ?CapabilityResource $capability = null;

    /**
     * @var array <int, CapabilityActionResource>
     */
    public array $capabilityActions = [];

    /**
     * @var array <int, CapabilityStateResource>
     */
    public array $capabilityStates = [];
}
