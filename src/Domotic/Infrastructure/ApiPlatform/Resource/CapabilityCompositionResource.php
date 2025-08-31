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
use ApiPlatform\Metadata\Post;
use App\Domotic\Domain\Model\CapabilityComposition;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @todo update or delete on ne peut pas supprimer une composition gérer par un plugin
 * On peut uniquement agir sur des composition custom (à définir process de création d'un virtual device)
 */
#[ApiResource(
    shortName: 'capability_composition',
    operations: [
        new GetCollection(),
        new Get(),
        new Post(security: 'is_granted("ROLE_ADMIN")'),
        new Patch(security: 'is_granted("ROLE_ADMIN")'),
        new Delete(security: 'is_granted("ROLE_ADMIN")'),
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
    public ?string $id = null;

    public ?CapabilityResource $capability = null;

    /**
     * @var array <int, CapabilityActionResource>
     */
    #[Assert\All([new Assert\Type(type: CapabilityActionResource::class)])]
    public array $capabilityActions = [];

    /**
     * @var array <int, CapabilityStateResource>
     */
    #[Assert\All([new Assert\Type(type: CapabilityStateResource::class)])]
    public array $capabilityStates = [];
}
