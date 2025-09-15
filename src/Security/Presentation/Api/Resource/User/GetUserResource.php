<?php
namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use DateTimeInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\Resource\UserStatus\UserStatusResource;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;

#[ApiResource(
    shortName: 'user',
    operations: [
        new GetCollection(security: 'is_granted("ROLE_ADMIN")'),
        new Get(security: 'is_granted("ROLE_ADMIN") or object.id == user.id')
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
final readonly class GetUserResource
{
    public function __construct(
        #[ApiProperty(writable: false, identifier: true)]
        public ?string $id = null,
        public string $email,
        public string $firstname,
        public string $lastname,
        public array $roles,
        public UserTypeResource   $type,
        public UserStatusResource $status,
        public DateTimeInterface  $createdAt,
        public DateTimeInterface  $updatedAt
    ) {
    }
}
