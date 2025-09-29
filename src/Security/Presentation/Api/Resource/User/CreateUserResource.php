<?php

namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Infrastructure\Framework\Symfony\Validator\EmailExist;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Marvin\Security\Presentation\Api\State\Processor\CreateUserProcessor;
use Marvin\Shared\Domain\Application;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'user',
    operations: [new Post()],
    routePrefix: '/security',
    output: ReadUserResource::class,
    security: 'is_granted("ROLE_ADMIN")',
    provider: EntityToApiStateProvider::class,
    processor: CreateUserProcessor::class,
    stateOptions: new Options(entityClass: User::class),
)]
final class CreateUserResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    #[Assert\Email]
    #[Assert\NotBlank]
    #[EmailExist]
    public string $email;

    #[Assert\NotBlank]
    public string $firstname;

    #[Assert\NotBlank]
    public string $lastname;

    #[Assert\NotBlank]
    public array $roles;

    #[Assert\Choice(choices: Application::APP_AVAILABLE_LOCALES)]
    public string $locale;

    #[Assert\Choice(choices: Application::APP_AVAILABLE_THEMES)]
    public string $theme;

    #[Assert\NotBlank]
    public UserTypeResource $type;

    #[Assert\NotBlank]
    #[Assert\PasswordStrength]
    public ?string $password = null;
}
