<?php

namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\State\Processor\ChangePasswordUserProcessor;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[Map(source: User::class)]
#[ApiResource(
    shortName: 'user',
    operations: [
        new Patch(
            uriTemplate: '/users/{id}/change-password',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            output: ReadUserResource::class,
            processor: ChangePasswordUserProcessor::class
        ),
    ],
    routePrefix: '/security',
    output: ReadUserResource::class,
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: User::class),
)]
final class ChangePasswordUserResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    #[NotBlank]
    public string $currentPassword;

    #[NotBlank]
    #[PasswordStrength]
    public string $newPassword;
}
