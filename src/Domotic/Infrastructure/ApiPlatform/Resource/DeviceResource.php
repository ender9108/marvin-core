<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domotic\Domain\Model\Device;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'device',
    operations: [
        new GetCollection(),
        new Get(),
    ],
    routePrefix: 'domotic',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: Device::class)
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'technicalName' => 'exact',
    'capabilityCompositions.capability.reference' => 'exact',
    'protocol.id' => 'exact',
    'protocol.reference' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'name', 'protocol.label'])]
final class DeviceResource implements ApiResourceInterface
{
    use ResourceBlameableTrait;
    use ResourceTimestampableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?string $id = null;

    #[Assert\NotNull]
    #[Assert\Length(min: 5, max: 255)]
    public ?string $name = null;

    #[ApiProperty(readable: true, writable: false)]
    public ?string $technicalName = null;

    #[Assert\Count(min: 1)]
    public array $capabilityCompositions = [];

    #[Assert\NotNull]
    public ?ProtocolResource $protocol = null;
}
