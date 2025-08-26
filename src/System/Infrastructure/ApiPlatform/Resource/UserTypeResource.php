<?php

namespace App\System\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\System\Domain\Model\UserType;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;

#[ApiResource(
    shortName: 'user_type',
    operations: [
        new GetCollection(),
        new Get()
    ],
    routePrefix: 'system',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: UserType::class)
)]
class UserTypeResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?string $id = null;

    public ?string $label = null;

    public ?string $reference = null;
}
