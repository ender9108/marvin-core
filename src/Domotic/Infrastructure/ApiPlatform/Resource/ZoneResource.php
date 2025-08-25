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
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;
use Symfony\Component\JsonStreamer\Attribute\JsonStreamable;
use Symfony\Component\Validator\Constraints as Assert;

#[JsonStreamable]
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
    public ?string $id = null;

    #[Assert\NotNull]
    #[Assert\Length(min: 5, max: 255)]
    public ?string $label = null;

    #[Assert\GreaterThanOrEqual(0)]
    public float $area = 0;
}
