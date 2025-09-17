<?php

namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\State\Processor\ChangeUserPasswordProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'user',
    operations: [
        new Patch(
            uriTemplate: '/users/{id}/change-password',
            security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
            output: GetUserResource::class,
            processor: ChangeUserPasswordProcessor::class
        )
    ],
    routePrefix: '/security',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: User::class)
)]
final class ChangeUserPasswordResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    public function __construct(
        #[Assert\NotBlank]
        public ?string $currentPassword,
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\PasswordStrength]
        public ?string $newPassword,
    ) {
    }
}
