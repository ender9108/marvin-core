<?php

namespace App\System\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\System\Domain\Model\User;
use App\System\Infrastructure\ApiPlatform\State\Provider\MeProvider;
use DateTimeInterface;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;
use Symfony\Component\JsonStreamer\Attribute\JsonStreamable;
use Symfony\Component\Validator\Constraints as Assert;

#[JsonStreamable]
#[ApiResource(
    shortName: 'user',
    operations: [
        new GetCollection(security: 'is_granted("ROLE_SUPER_ADMIN")'),
        new Get(security: "is_granted('CAN_VIEW', object)"),
        new Get(uriTemplate: '/users/me', provider: MeProvider::class),
        new Post(security: 'is_granted("ROLE_SUPER_ADMIN")',),
        new Patch(security: "is_granted('CAN_UPDATE', object)",),
        new Put(security: "is_granted('CAN_UPDATE', object)",),
        new Delete(security: 'is_granted("ROLE_SUPER_ADMIN")')
    ],
    routePrefix: '/system',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: User::class)
)]
class UserResource implements ApiResourceInterface
{
    use ResourceTimestampableTrait;
    use ResourceBlameableTrait;

    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?string $id = null;

    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $firstName = null;

    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 255)]
    public ?string $lastName = null;

    #[Assert\NotNull]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotNull]
    #[Assert\Count(min: 1)]
    public array $roles = ['ROLE_USER'];

    #[Assert\NotNull]
    public ?UserTypeResource $type = null;

    #[Assert\NotNull]
    public ?UserStatusResource $status = null;

    #[Assert\NotNull]
    #[ApiProperty(readable: false)]
    public ?string $password = null;
}
