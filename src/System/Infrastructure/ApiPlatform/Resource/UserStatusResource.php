<?php

namespace App\System\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\System\Domain\Model\UserStatus;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;
use Symfony\Component\JsonStreamer\Attribute\JsonStreamable;
use Symfony\Component\Validator\Constraints as Assert;

#[JsonStreamable]
#[ApiResource(
    shortName: 'user_status',
    operations: [
        new GetCollection(),
        new Get()
    ],
    routePrefix: 'system',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: UserStatus::class)
)]
class UserStatusResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?string $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 128)]
    public ?string $label = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 64)]
    public ?string $reference = null;
}
