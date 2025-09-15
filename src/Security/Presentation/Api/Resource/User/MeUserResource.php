<?php
namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use Marvin\Security\Presentation\Api\State\Provider\MeUserProvider;

#[Get(
    uriTemplate: '/users/me',
    routePrefix: '/security',
    shortName: 'user',
    normalizationContext: ['skip_null_values' => false],
    security: 'is_granted("ROLE_USER")',
    provider: MeUserProvider::class
)]
final readonly class MeUserResource implements UserResourceInterface
{
    public function __construct(
        #[ApiProperty(writable: false, identifier: true)]
        public ?string $id = null,
        public string $email,
        public string $firstname,
        public string $lastname,
        public array $roles
    ) {
    }
}
