<?php

namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\State\Processor\UpdateProfileUserProcessor;
use Marvin\Shared\Domain\Application;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints\Choice;

#[Map(source: User::class)]
#[ApiResource(
    shortName: 'user',
    operations: [
        new Patch(
            uriTemplate: '/users/{id}/update-profile',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            output: ReadUserResource::class,
            processor: UpdateProfileUserProcessor::class
        ),
    ],
    routePrefix: '/security',
    output: ReadUserResource::class,
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: User::class),
)]
final class UpdateProfileUserResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    public ?string $firstname = null;

    public ?string $lastname = null;

    #[Choice(Application::APP_AVAILABLE_THEMES)]
    public ?string $theme = null;

    #[Choice(Application::APP_AVAILABLE_LOCALES)]
    public ?string $locale = null;
}
