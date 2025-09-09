<?php
namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\Model\UserType;
use Marvin\Security\Presentation\Api\Resource\UserStatus\GetUserStatusResource;
use Marvin\Security\Presentation\Api\Resource\UserType\GetUserTypeResource;
use Marvin\Security\Presentation\Api\State\Provider\MeUserProvider;
use Marvin\Security\Presentation\Api\Transformer\ValueObjectTransformer;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Get(
    uriTemplate: '/users/me',
    routePrefix: '/security',
    shortName: 'user',
    normalizationContext: ['skip_null_values' => false],
    security: 'is_granted("ROLE_ADMIN")',
    provider: MeUserProvider::class
)]
#[Map(source: User::class)]
final class MeUserResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public ?string $id = null;

    public function __construct(
        public readonly string $email,
        public readonly string $firstname,
        public readonly string $lastname,
        #[Map(source: 'roles', transform: [ValueObjectTransformer::class, 'transformRolesToArray'])]
        public readonly array $roles,
        #[Map(target: GetUserTypeResource::class, source: 'user.type')]
        public readonly ?GetUserTypeResource $type,
        #[Map(target: GetUserStatusResource::class, source: 'user.status')]
        public readonly ?GetUserStatusResource $status,
    ) {
    }
}
