<?php

namespace App\System\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\System\Domain\Model\UserType;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
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
#[ApiFilter(SearchFilter::class, properties: [
    'label' => 'partial',
    'reference' => 'exact'
])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'label', 'reference'])]
class UserTypeResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?string $id = null;

    public ?string $label = null;

    public ?string $reference = null;
}
