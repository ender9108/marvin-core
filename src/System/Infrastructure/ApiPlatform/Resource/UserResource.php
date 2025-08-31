<?php

namespace App\System\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\System\Domain\Model\User;
use App\System\Infrastructure\ApiPlatform\State\Provider\MeProvider;
use EnderLab\BlameableBundle\Trait\ApiPlatform\ResourceBlameableTrait;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Symfony\Security\Attribute\FieldUpdatableBy;
use EnderLab\TimestampableBundle\Trait\ApiPlatform\ResourceTimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'user',
    operations: [
        new GetCollection(security: 'is_granted("ROLE_ADMIN")'),
        new Get(uriTemplate: '/users/me', provider: MeProvider::class),
        new Get(security: 'is_granted("CAN_VIEW", object)'),
        new Post(
            security: 'is_granted("ROLE_ADMIN")',
            validationContext: ['groups' => ['Default', 'postValidation']],
        ),
        new Patch(
            security: 'is_granted("CAN_UPDATE", object)',
            validationContext: ['groups' => ['Default', 'patchValidation']],
        ),
        new Delete(security: 'is_granted("ROLE_ADMIN")')
    ],
    routePrefix: '/system',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: User::class)
)]
#[ApiFilter(SearchFilter::class, properties: [
    'firstName' => 'partial',
    'lastName' => 'partial',
    'email' => 'partial',
    'type.reference' => 'exact',
    'status.reference' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id',
    'firstName',
    'lastName',
    'email',
    'type.label',
    'status.label'
])]
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
    #[FieldUpdatableBy(roles: ['ROLE_ADMIN'])]
    public array $roles = ['ROLE_USER'];

    #[Assert\NotNull]
    #[FieldUpdatableBy(roles: ['ROLE_ADMIN'])]
    public ?UserTypeResource $type = null;

    #[Assert\NotNull]
    #[FieldUpdatableBy(roles: ['ROLE_ADMIN'])]
    public ?UserStatusResource $status = null;

    #[ApiProperty(readable: false)]
    #[Assert\NotNull(groups: ['postValidation'])]
    #[Assert\Length(min: 10, max: 128, groups: ['postValidation'])]
    #[Assert\NotCompromisedPassword(groups: ['postValidation'])]
    public ?string $password = null;
}
