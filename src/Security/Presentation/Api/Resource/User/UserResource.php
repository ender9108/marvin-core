<?php

namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use DateTimeInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\List\Role;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\Resource\UserStatus\UserStatusResource;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Marvin\Security\Presentation\Api\State\Processor\ChangeUserEmailProcessor;
use Marvin\Security\Presentation\Api\State\Processor\ChangeUserPasswordProcessor;
use Marvin\Security\Presentation\Api\State\Processor\CreateUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\DeleteUserProcessor;

#[ApiResource(
    shortName: 'user',
    operations: [
        new GetCollection(security: 'is_granted("ROLE_ADMIN")'),
        new Get(security: 'is_granted("ROLE_ADMIN") or object.id == user.id'),
        new Post(
            security: 'is_granted("ROLE_ADMIN")',
            processor: CreateUserProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/{id}/change-email',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            processor: ChangeUserEmailProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/{id}/change-password',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            processor: ChangeUserPasswordProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/{id}/update-profile',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            processor: ChangeUserPasswordProcessor::class
        ),
        new Delete(processor: DeleteUserProcessor::class)
    ],
    routePrefix: '/security',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: User::class)
)]
#[ApiFilter(OrderFilter::class, properties: [
    'id',
    'firstname.value',
    'lastname.value',
    'type.label.value',
    'status.label.value'
])]
final class UserResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    public function __construct(
        public string $email,
        public string $firstname,
        public string $lastname,
        /** @var array<Role> */
        public array $roles,
        public UserTypeResource $type,
        public UserStatusResource $status,
        public DateTimeInterface $createdAt,
        public DateTimeInterface $updatedAt
    ) {
    }
}
