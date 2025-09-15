<?php
namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Post;
use Marvin\Security\Domain\List\Role;
use Marvin\Security\Domain\List\UserTypeReference;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Marvin\Security\Presentation\Api\State\Processor\CreateUserProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[Post(
    routePrefix: '/security',
    shortName: 'user',
    normalizationContext: ['skip_null_values' => false],
    security: 'is_granted("ROLE_ADMIN")',
    processor: CreateUserProcessor::class
)]
final readonly class CreateUserResource implements UserResourceInterface
{
    public function __construct(
        #[ApiProperty(writable: false, identifier: true)]
        public ?string $id = null,
        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $email,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $firstname,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public readonly string $lastname,
        #[Assert\All(
            new Assert\Choice(choices: [
                Role::USER->value,
                Role::ADMIN->value,
                Role::SUPER_ADMIN->value
            ])
        )]
        public readonly array $roles,
        #[Assert\Choice(choices: [
            UserTypeReference::TYPE_APPLICATION->value,
            UserTypeReference::TYPE_SYSTEM->value,
            UserTypeReference::TYPE_CLI->value
        ])]
        public readonly ?UserTypeResource $type,
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\PasswordStrength]
        public readonly ?string $password,
    ) {
    }
}
