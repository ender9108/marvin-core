<?php

namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use DateTimeInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Infrastructure\Framework\Symfony\MapperTransformer\ArrayValueObjectTransformer;
use Marvin\Security\Presentation\Api\Resource\UserStatus\UserStatusResource;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Marvin\Security\Presentation\Api\State\Processor\DeleteUserProcessor;
use Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer\DatetimeValueObjectTransformer;
use Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer\SubResourceTransformer;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: User::class)]
#[ApiResource(
    shortName: 'user',
    operations: [
        new Get(security: 'is_granted("ROLE_ADMIN") or object.id == user.id'),
        new GetCollection(
            security: 'is_granted("ROLE_ADMIN")',
            parameters: [
                ':property' => new QueryParameter(filter: new PartialSearchFilter(), properties: ['email', 'firstname.value', 'lastname.value']),
                //'type.reference.value' => new QueryParameter(filter: new ExactFilter()),
                //'status.reference.value' => new QueryParameter(filter: new ExactFilter()),
                'order[:property]' => new QueryParameter(filter: new OrderFilter(), properties: ['id', 'firstname.value', 'lastname.value', 'createdAt.value']),
            ]
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
            processor: DeleteUserProcessor::class,
        )
    ],
    routePrefix: '/security',
    output: ReadUserResource::class,
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
    #[Map(transform: ArrayValueObjectTransformer::class)]
    public array $roles;

    #[ApiProperty(writable: false)]
    public string $locale;

    #[ApiProperty(writable: false)]
    public string $theme;

    #[ApiProperty(writable: false)]
    #[Map(transform: SubResourceTransformer::class)]
    public UserTypeResource $type;

    #[ApiProperty(writable: false)]
    #[Map(transform: SubResourceTransformer::class)]
    public UserStatusResource $status;

    #[ApiProperty(writable: false)]
    #[Map(transform: DatetimeValueObjectTransformer::class)]
    public DateTimeInterface $createdAt;

    #[ApiProperty(writable: false)]
    #[Map(transform: DatetimeValueObjectTransformer::class)]
    public DateTimeInterface $updatedAt;
}
