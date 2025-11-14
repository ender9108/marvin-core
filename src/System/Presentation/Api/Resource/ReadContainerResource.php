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

namespace Marvin\System\Presentation\Api\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use DateTimeInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Shared\Infrastructure\Framework\Symfony\MapperTransformer\EnumTransformer;
use Marvin\System\Domain\Model\Container;
use Marvin\System\Presentation\Api\Dto\Input\ExecContainerDto;
use Marvin\System\Presentation\Api\State\Processor\ExecContainerProcessor;
use Marvin\System\Presentation\Api\State\Processor\RestartContainerProcessor;
use Marvin\System\Presentation\Api\State\Processor\StartContainerProcessor;
use Marvin\System\Presentation\Api\State\Processor\StopContainerProcessor;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: Container::class)]
#[ApiResource(
    shortName: 'container',
    operations: [
        new Get(security: 'is_granted("ROLE_ADMIN")'),
        new GetCollection(security: 'is_granted("ROLE_ADMIN")'),
        new Post(
            uriTemplate: '/containers/{id}/start',
            security: 'is_granted("ROLE_ADMIN")',
            processor: StartContainerProcessor::class,
        ),
        new Post(
            uriTemplate: '/containers/{id}/restart',
            security: 'is_granted("ROLE_ADMIN")',
            processor: RestartContainerProcessor::class,
        ),
        new Post(
            uriTemplate: '/containers/{id}/stop',
            security: 'is_granted("ROLE_ADMIN")',
            processor: StopContainerProcessor::class,
        ),
        new Post(
            uriTemplate: '/containers/{id}/exec',
            security: 'is_granted("ROLE_ADMIN")',
            input: ExecContainerDto::class,
            processor: ExecContainerProcessor::class,
        )
    ],
    routePrefix: '/system',
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: Container::class),
)]
class ReadContainerResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    #[ApiProperty(writable: false)]
    public string $serviceLabel;

    #[ApiProperty(writable: false)]
    public string $type;

    #[ApiProperty(writable: false)]
    public string $uptime;

    #[ApiProperty(writable: false)]
    public string $containerId;

    #[ApiProperty(writable: false)]
    public string $containerLabel;

    #[ApiProperty(writable: false)]
    public ?DateTimeInterface $lastSyncedAt;

    #[ApiProperty(writable: false)]
    public DateTimeInterface $createdAt;
}
