<?php

namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Patch;
use Marvin\Security\Presentation\Api\State\Processor\ChangeUserPasswordProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[Patch(
    uriTemplate: '/users/{id}/update-profile',
    routePrefix: '/security',
    shortName: 'user',
    normalizationContext: ['skip_null_values' => false],
    security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
    processor: ChangeUserPasswordProcessor::class
)]
final class UpdateUserProfileResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    public function __construct(
        #[Assert\NotBlank]
        public ?string $currentPassword,
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\PasswordStrength]
        public ?string $password,
    ) {
    }
}
