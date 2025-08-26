<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domotic\Domain\Model\Capability;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;

#[ApiResource(
    shortName: 'capability',
    operations: [
        new GetCollection(),
        new Get(),
    ],
    routePrefix: 'domotic',
    normalizationContext: [
        'skip_null_values' => false,
    ],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: Capability::class)
)]
final class CapabilityResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?string $id = null;

    public ?string $label = null;

    public ?string $reference = null;
}
