<?php
namespace Marvin\Security\Presentation\Api\Resource\UserType;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use DateTimeInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Security\Domain\Model\UserType;

#[ApiResource(
    shortName: 'user_type',
    operations: [
        new GetCollection(),
        new Get()
    ],
    routePrefix: '/security',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: UserType::class)
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'label.value', 'reference.value'])]
final readonly class UserTypeResource
{
    public function __construct(
        #[ApiProperty(writable: false, identifier: true)]
        public string $id,
        public string $label,
        public string $reference,
        public DateTimeInterface $createdAt,
    ) {
    }
}
