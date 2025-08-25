<?php

namespace App\System\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\System\Domain\Model\PluginStatus;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsApiPlatformBundle\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsApiPlatformBundle\State\Provider\EntityToApiStateProvider;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;
use Symfony\Component\JsonStreamer\Attribute\JsonStreamable;

#[JsonStreamable]
#[ApiResource(
    shortName: 'plugin_status',
    operations: [
        new GetCollection(),
        new Get(),
    ],
    routePrefix: 'system',
    normalizationContext: ['skip_null_values' => false,],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: PluginStatus::class)
)]
final class PluginStatusResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?string $id = null;

    public ?string $label = null;

    public ?string $reference = null;
}
