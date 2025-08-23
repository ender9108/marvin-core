<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Domotic\Domain\Model\Zone;
use EnderLab\DddCqrsApiPlatformBundle\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsApiPlatformBundle\State\Provider\EntityToApiStateProvider;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\Trait\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\Trait\ResourceTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'zone',
    operations: [
        new GetCollection(),
        new Get(),
        new Put(),
        new Patch(),
    ],
    routePrefix: 'domotic',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: Zone::class)
)]
final class ZoneResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?int $id = null;

    #[Assert\NotNull]
    #[Assert\Length(min: 5, max: 255)]
    public ?string $label = null;

    #[Assert\GreaterThanOrEqual(0)]
    public float $area = 0;
}
