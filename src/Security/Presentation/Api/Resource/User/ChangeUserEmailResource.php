<?php

namespace Marvin\Security\Presentation\Api\Resource\User;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\State\Processor\ChangeUserEmailProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'user',
    operations: [
       new Patch(
           uriTemplate: '/users/{id}/change-email',
           security: 'is_granted("ROLE_ADMIN") or object.id == user.id',
           output: GetUserResource::class,
           processor: ChangeUserEmailProcessor::class
       )
    ],
    routePrefix: '/security',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: User::class)
)]
final class ChangeUserEmailResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
    ) {
    }
}
