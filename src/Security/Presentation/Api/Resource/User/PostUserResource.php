<?php
namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Post;
use Marvin\Security\Domain\List\Role;
use Marvin\Security\Domain\List\UserTypeReference;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Model\UserType;
use Marvin\Security\Presentation\Api\Resource\UserType\GetUserTypeResource;
use Marvin\Security\Presentation\Api\State\Processor\CreateUserProcessor;
use Marvin\Security\Presentation\Api\Transformer\ValueObjectTransformer;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Post(
    routePrefix: '/security',
    shortName: 'user',
    normalizationContext: ['skip_null_values' => false],
    security: 'is_granted("ROLE_ADMIN")',
    processor: CreateUserProcessor::class
)]
#[Map(source: User::class)]
final class PostUserResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public ?string $id = null;

    public function __construct(
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
        #[Map(source: 'roles', transform: [ValueObjectTransformer::class, 'transformRolesToArray'])]
        public readonly array $roles,
        #[Assert\Choice(choices: [
            UserTypeReference::TYPE_APPLICATION->value,
            UserTypeReference::TYPE_SYSTEM->value,
            UserTypeReference::TYPE_CLI->value
        ])]
        #[Map(target: GetUserTypeResource::class, source: UserType::class)]
        public readonly ?GetUserTypeResource $type,
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\PasswordStrength]
        public readonly string $password,
    ) {
    }
}
