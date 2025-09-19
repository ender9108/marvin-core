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
use Marvin\Security\Infrastructure\Framework\Symfony\Validator\EmailExist;
use Marvin\Security\Presentation\Api\Resource\UserStatus\UserStatusResource;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Marvin\Security\Presentation\Api\State\Processor\ChangeUserEmailProcessor;
use Marvin\Security\Presentation\Api\State\Processor\ChangeUserPasswordProcessor;
use Marvin\Security\Presentation\Api\State\Processor\CreateUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\DeleteUserProcessor;
use Marvin\Security\Presentation\Api\State\Provider\MeUserProvider;
use Marvin\Shared\Domain\Application;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'user',
    operations: [
        new Get(
            uriTemplate: '/users/me',
            security: 'is_granted("ROLE_USER")',
            provider: MeUserProvider::class
        ),
        new GetCollection(security: 'is_granted("ROLE_ADMIN")'),
        new Get(security: 'is_granted("ROLE_ADMIN") or object.id == user.id'),
        new Post(
            denormalizationContext: ['groups' => ['postCreateUser']],
            security: 'is_granted("ROLE_ADMIN")',
            validationContext: ['groups' => ['postCreateUser']],
            processor: CreateUserProcessor::class,
        ),
        new Patch(
            uriTemplate: '/users/{id}/change-email',
            denormalizationContext: ['groups' => ['patchChangeEmailUser']],
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            validationContext: ['groups' => ['patchChangeEmailUser']],
            processor: ChangeUserEmailProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/{id}/change-password',
            denormalizationContext: ['groups' => ['patchChangePasswordUser']],
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            validationContext: ['groups' => ['patchChangePasswordUser']],
            processor: ChangeUserPasswordProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/{id}/update-profile',
            denormalizationContext: ['groups' => ['patchUpdateProfileUser']],
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            validationContext: ['groups' => ['patchUpdateProfileUser']],
            processor: ChangeUserPasswordProcessor::class
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
            processor: DeleteUserProcessor::class,
        )
    ],
    routePrefix: '/security',
    normalizationContext: [
        'skip_null_values' => false
    ],
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

    #[ApiProperty(writable: false)]
    public DateTimeInterface $createdAt;

    #[ApiProperty(writable: false)]
    public DateTimeInterface $updatedAt;

    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank(groups: ['postCreateUser', 'patchChangeEmailUser'])]
        #[EmailExist(groups: ['postCreateUser', 'patchChangeEmailUser'])]
        #[Groups(groups: ['postCreateUser', 'patchChangeEmailUser'])]
        public string $email,

        #[Groups(groups: ['postCreateUser', 'patchUpdateProfileUser'])]
        #[Assert\NotBlank(groups: ['postCreateUser', 'patchUpdateProfileUser'])]
        public string $firstname,

        #[Groups(groups: ['postCreateUser', 'patchUpdateProfileUser'])]
        #[Assert\NotBlank(groups: ['postCreateUser', 'patchUpdateProfileUser'])]
        public string $lastname,

        /** @var array<Role> */
        #[Groups(groups: ['postCreateUser'])]
        #[Assert\NotBlank(groups: ['postCreateUser'])]
        public array $roles,

        #[Groups(groups: ['postCreateUser', 'patchUpdateProfileUser'])]
        #[Assert\Choice(choices: Application::APP_AVAILABLE_LOCALES, groups: ['postCreateUser', 'patchUpdateProfileUser'])]
        public string $locale = 'fr',

        #[Groups(groups: ['postCreateUser', 'patchUpdateProfileUser'])]
        #[Assert\Choice(choices: Application::APP_AVAILABLE_THEMES, groups: ['postCreateUser', 'patchUpdateProfileUser'])]
        public string $theme = 'dark',

        #[Groups(groups: ['postCreateUser'])]
        #[Assert\NotBlank(groups: ['postCreateUser'])]
        public UserTypeResource $type,

        #[ApiProperty(readable: true, writable: false)]
        public ?UserStatusResource $status = null,

        #[Groups(groups: ['postCreateUser'])]
        #[Assert\NotBlank(groups: ['postCreateUser'])]
        #[ApiProperty(readable: false)]
        public ?string $password = null,

        #[Groups(groups: ['patchChangePasswordUser'])]
        #[Assert\NotBlank(groups: ['patchChangePasswordUser'])]
        #[ApiProperty(readable: false)]
        public ?string $currentPassword = null,

        #[Groups(groups: ['patchChangePasswordUser'])]
        #[Assert\NotBlank(groups: ['patchChangePasswordUser'])]
        #[ApiProperty(readable: false)]
        public ?string $newPassword = null,
    ) {
    }
}
