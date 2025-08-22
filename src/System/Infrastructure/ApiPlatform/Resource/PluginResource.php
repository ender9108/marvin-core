<?php

namespace App\System\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\System\Domain\Model\Plugin;
use EnderLab\DddCqrsApiPlatformBundle\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\Attribute\AsTranslatableApiProperty;
use EnderLab\DddCqrsApiPlatformBundle\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsApiPlatformBundle\State\Provider\EntityToApiStateProvider;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\Trait\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\Trait\ResourceTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'plugin',
    operations: [
        new GetCollection(),
        new Get(),
    ],
    routePrefix: 'system',
    normalizationContext: ['skip_null_values' => false,],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: Plugin::class)
)]
final class PluginResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?string $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 128)]
    #[AsTranslatableApiProperty]
    public ?string $label = null;

    public ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 64)]
    public ?string $reference = null;

    #[Assert\NotBlank]
    public ?string $version = null;

    #[Assert\NotNull]
    public ?PluginStatusResource $status = null;
}
