<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Secret\Presentation\Api\Resource;

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
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Presentation\Api\Dto\CreateSecretDto;
use Marvin\Secret\Presentation\Api\Dto\UpdateSecretDto;
use Marvin\Secret\Presentation\Api\State\Processor\CreateSecretProcessor;
use Marvin\Secret\Presentation\Api\State\Processor\DeleteSecretProcessor;
use Marvin\Secret\Presentation\Api\State\Processor\UpdateSecretProcessor;

#[ApiResource(
    shortName: 'secret',
    operations: [
        new Get(security: 'is_granted("ROLE_ADMIN")'),
        new GetCollection(security: 'is_granted("ROLE_ADMIN")'),
        new Post(
            security: 'is_granted("ROLE_ADMIN")',
            input: CreateSecretDto::class,
            processor: CreateSecretProcessor::class
        ),
        new Patch(
            security: 'is_granted("ROLE_ADMIN")',
            input: UpdateSecretDto::class,
            processor: UpdateSecretProcessor::class
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
            processor: DeleteSecretProcessor::class
        ),
    ],
    routePrefix: '/secret',
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: Secret::class)
)]
final class ReadSecretResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    #[ApiProperty(writable: false)]
    public string $key;

    #[ApiProperty(writable: false)]
    public string $scope;

    #[ApiProperty(writable: false)]
    public string $category;

    #[ApiProperty(writable: false)]
    public ?string $management = null;

    #[ApiProperty(writable: false)]
    public ?int $rotationIntervalDays = null;

    #[ApiProperty(writable: false)]
    public ?bool $autoRotate = null;

    #[ApiProperty(writable: false)]
    public ?string $rotationCommand = null;

    #[ApiProperty(writable: false)]
    public ?DateTimeInterface $lastRotatedAt = null;

    #[ApiProperty(writable: false)]
    public ?DateTimeInterface $expiresAt = null;

    #[ApiProperty(writable: false)]
    public ?array $metadata = null;

    #[ApiProperty(writable: false)]
    public ?DateTimeInterface $updatedAt = null;

    #[ApiProperty(writable: false)]
    public ?DateTimeInterface $createdAt = null;
}
