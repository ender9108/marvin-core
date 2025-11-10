<?php

namespace Marvin\Security\Presentation\Api\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use DateTimeInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Infrastructure\Framework\Symfony\MapperTransformer\RolesTransformer;
use Marvin\Security\Presentation\Api\Dto\Input\ChangeEmailUserDto;
use Marvin\Security\Presentation\Api\Dto\Input\ChangePasswordUserDto;
use Marvin\Security\Presentation\Api\Dto\Input\CreateUserDto;
use Marvin\Security\Presentation\Api\Dto\Input\RequestResetPasswordUserDto;
use Marvin\Security\Presentation\Api\Dto\Input\ResetPasswordUserDto;
use Marvin\Security\Presentation\Api\Dto\Input\UpdateProfileUserDto;
use Marvin\Security\Presentation\Api\State\Processor\ChangeEmailUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\ChangePasswordUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\CreateUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\DeleteUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\DisableUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\EnableUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\LockUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\RequestResetPasswordUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\ResetPasswordUserProcessor;
use Marvin\Security\Presentation\Api\State\Processor\UpdateProfileUserProcessor;
use Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer\EnumTransformer;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: User::class)]
#[ApiResource(
    shortName: 'user',
    operations: [
        new Get(security: 'is_granted("ROLE_ADMIN") or object.id == user.id'),
        new GetCollection(
            security: 'is_granted("ROLE_ADMIN")',
            /**
             * @todo revoir les filtres
             */
            /*parameters: [
                ':property' => new QueryParameter(filter: new PartialSearchFilter(), properties: ['email', 'firstname.value', 'lastname.value']),
                'order[:property]' => new QueryParameter(filter: new OrderFilter(), properties: ['id', 'firstname.value', 'lastname.value', 'createdAt.value']),
            ]*/
        ),
        new Post(
            security: 'is_granted("ROLE_ADMIN")',
            input: CreateUserDto::class,
            processor: CreateUserProcessor::class,
        ),
        new Post(
            uriTemplate: '/users/{id}/request-reset-password',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            input: RequestResetPasswordUserDto::class,
            processor: RequestResetPasswordUserProcessor::class,
        ),
        new Post(
            uriTemplate: '/users/{id}/reset-password',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            input: ResetPasswordUserDto::class,
            processor: ResetPasswordUserProcessor::class,
        ),
        new Patch(
            uriTemplate: '/users/{id}/change-email',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            input: ChangeEmailUserDto::class,
            processor: ChangeEmailUserProcessor::class,
        ),
        new Patch(
            uriTemplate: '/users/{id}/change-password',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            input: ChangePasswordUserDto::class,
            processor: ChangePasswordUserProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/{id}/update-profile',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            input: UpdateProfileUserDto::class,
            processor: UpdateProfileUserProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/{id}/enable-user',
            security: 'is_granted("ROLE_ADMIN")',
            processor: EnableUserProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/{id}/disable-user',
            security: 'is_granted("ROLE_ADMIN")',
            processor: DisableUserProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/{id}/lock-user',
            security: 'is_granted("ROLE_ADMIN")',
            processor: LockUserProcessor::class
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
            processor: DeleteUserProcessor::class
        ),
    ],
    routePrefix: '/security',
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: User::class),
)]
final class ReadUserResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    #[ApiProperty(writable: false)]
    public string $email;

    #[ApiProperty(writable: false)]
    public string $firstname;

    #[ApiProperty(writable: false)]
    public string $lastname;

    #[ApiProperty(writable: false)]
    #[Map(transform: RolesTransformer::class)]
    public array $roles;

    #[ApiProperty(writable: false)]
    public string $locale;

    #[ApiProperty(writable: false)]
    public string $theme;

    #[ApiProperty(writable: false)]
    public string $timezone;

    #[ApiProperty(writable: false)]
    #[Map(transform: EnumTransformer::class)]
    public string $type;

    #[ApiProperty(writable: false)]
    #[Map(transform: EnumTransformer::class)]
    public string $status;

    #[ApiProperty(writable: false)]
    public DateTimeInterface $createdAt;

    #[ApiProperty(writable: false)]
    public ?DateTimeInterface $updatedAt = null;
}
