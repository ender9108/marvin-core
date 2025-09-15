<?php
namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Post;
use Marvin\Security\Presentation\Api\State\Processor\ChangeUserPasswordProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[Post(
    uriTemplate: '/books/{id}/change-password',
    routePrefix: '/security',
    shortName: 'user',
    normalizationContext: ['skip_null_values' => false],
    security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
    processor: ChangeUserPasswordProcessor::class
)]
final readonly class ChangeUserPasswordResource
{
    public function __construct(
        #[ApiProperty(writable: false, identifier: true)]
        public ?string $id = null,

        #[Assert\NotBlank]
        public ?string $currentPassword,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\PasswordStrength]
        public ?string $password,
    ) {
    }
}
