<?php
namespace Marvin\Security\Presentation\Api\Resource\UserType;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use DateTimeImmutable;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\UserType;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Get(
    routePrefix: '/security',
    shortName: 'user_type',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: UserType::class)
)]
#[Map(source: UserType::class)]
final class GetUserTypeResource
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
