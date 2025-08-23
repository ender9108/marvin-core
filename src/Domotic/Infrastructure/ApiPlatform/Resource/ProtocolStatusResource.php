<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domotic\Domain\Model\ProtocolStatus;
use EnderLab\DddCqrsApiPlatformBundle\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\Attribute\AsTranslatableApiProperty;
use EnderLab\DddCqrsApiPlatformBundle\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsApiPlatformBundle\State\Provider\EntityToApiStateProvider;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\Trait\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\Trait\ResourceTimestampableTrait;

#[ApiResource(
    shortName: 'protocol_status',
    operations: [
        new GetCollection(),
        new Get(),
    ],
    routePrefix: 'domotic',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: ProtocolStatus::class)
)]
final class ProtocolStatusResource implements ApiResourceInterface
{
    use ResourceBlameableTrait;
    use ResourceTimestampableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?int $id = null;

    #[AsTranslatableApiProperty]
    public ?string $label = null;

    public ?string $reference = null;
}
