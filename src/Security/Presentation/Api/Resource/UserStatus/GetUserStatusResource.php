<?php
namespace Marvin\Security\Presentation\Api\Resource\UserStatus;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\UserStatus;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Get(
    routePrefix: '/security',
    shortName: 'user_status',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: UserStatus::class)
)]
#[Map(source: UserStatus::class)]
final class GetUserStatusResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public ?string $id = null;

    public function __construct(
        public readonly string $label,
        public readonly string $reference,
        public readonly DateTimeImmutable $createdAt,
    ) {
    }
}
