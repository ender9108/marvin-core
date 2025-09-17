<?php

namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Post;
use Marvin\Security\Domain\List\Role;
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
final class CreateUserResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public string $firstname,
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 255)]
        public string $lastname,
        #[Assert\NotBlank]
        #[Assert\All(
            new Assert\Choice(choices: [
                Role::USER->value,
                Role::ADMIN->value,
                Role::SUPER_ADMIN->value
            ])
        )]
        /** @var array<int, string> $roles */
        public array $roles,
        #[Assert\NotNull]
        public ?UserTypeResource $type,
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\PasswordStrength]
        public ?string $password,
    ) {
    }
}
