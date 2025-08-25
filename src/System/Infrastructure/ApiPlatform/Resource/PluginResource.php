<?php

namespace App\System\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\System\Domain\Model\Plugin;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;
use Symfony\Component\JsonStreamer\Attribute\JsonStreamable;
use Symfony\Component\Validator\Constraints as Assert;

#[JsonStreamable]
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
final class PluginResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?string $id = null;

    #[ApiProperty(readable: true, writable: false)]
    public ?string $label = null;

    #[Assert\Length(max: 5000)]
    public ?string $description = null;

    #[ApiProperty(readable: true, writable: false)]
    public ?string $reference = null;

    #[Assert\NotBlank]
    public ?string $version = null;

    #[Assert\NotNull]
    public ?PluginStatusResource $status = null;
}
