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

namespace Marvin\Device\Presentation\Api\Resource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use DateTimeInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Presentation\Api\Dto\Input\AssignDeviceToZoneDto;
use Marvin\Device\Presentation\Api\Dto\Input\CreateGroupDto;
use Marvin\Device\Presentation\Api\Dto\Input\CreatePhysicalDeviceDto;
use Marvin\Device\Presentation\Api\Dto\Input\CreateSceneDto;
use Marvin\Device\Presentation\Api\Dto\Input\CreateVirtualDeviceDto;
use Marvin\Device\Presentation\Api\Dto\Input\ExecuteDeviceActionDto;
use Marvin\Device\Presentation\Api\Dto\Input\RegisterBridgeDeviceDto;
use Marvin\Device\Presentation\Api\State\Processor\ActivateSceneProcessor;
use Marvin\Device\Presentation\Api\State\Processor\AssignDeviceToZoneProcessor;
use Marvin\Device\Presentation\Api\State\Processor\CreateGroupProcessor;
use Marvin\Device\Presentation\Api\State\Processor\CreatePhysicalDeviceProcessor;
use Marvin\Device\Presentation\Api\State\Processor\CreateSceneProcessor;
use Marvin\Device\Presentation\Api\State\Processor\CreateVirtualDeviceProcessor;
use Marvin\Device\Presentation\Api\State\Processor\DeleteDeviceProcessor;
use Marvin\Device\Presentation\Api\State\Processor\ExecuteDeviceActionProcessor;
use Marvin\Device\Presentation\Api\State\Processor\RegisterBridgeDeviceProcessor;

#[ApiResource(
    shortName: 'device',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/devices/physical',
            input: CreatePhysicalDeviceDto::class,
            processor: CreatePhysicalDeviceProcessor::class,
        ),
        new Post(
            uriTemplate: '/devices/virtual',
            input: CreateVirtualDeviceDto::class,
            processor: CreateVirtualDeviceProcessor::class,
        ),
        new Post(
            uriTemplate: '/devices/group',
            input: CreateGroupDto::class,
            processor: CreateGroupProcessor::class,
        ),
        new Post(
            uriTemplate: '/devices/scene',
            input: CreateSceneDto::class,
            processor: CreateSceneProcessor::class,
        ),
        new Post(
            uriTemplate: '/devices/bridge',
            input: RegisterBridgeDeviceDto::class,
            processor: RegisterBridgeDeviceProcessor::class,
        ),
        new Post(
            uriTemplate: '/devices/{id}/assign-to-zone',
            input: AssignDeviceToZoneDto::class,
            processor: AssignDeviceToZoneProcessor::class,
        ),
        new Post(
            uriTemplate: '/devices/{id}/execute-action',
            input: ExecuteDeviceActionDto::class,
            processor: ExecuteDeviceActionProcessor::class,
        ),
        new Post(
            uriTemplate: '/devices/{id}/activate-scene',
            processor: ActivateSceneProcessor::class,
        ),
        new Delete(
            processor: DeleteDeviceProcessor::class,
        ),
    ],
    routePrefix: '/device',
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: Device::class),
)]
final class ReadDeviceResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    #[ApiProperty(writable: false)]
    public string $label;

    #[ApiProperty(writable: false)]
    public ?string $description = null;

    #[ApiProperty(writable: false)]
    public string $deviceType;

    #[ApiProperty(writable: false)]
    public string $status;

    // Physical device properties
    #[ApiProperty(writable: false)]
    public ?string $protocol = null;

    #[ApiProperty(writable: false)]
    public ?string $protocolId = null;

    #[ApiProperty(writable: false)]
    public ?string $physicalAddress = null;

    #[ApiProperty(writable: false)]
    public ?string $technicalName = null;

    // Composite device properties
    #[ApiProperty(writable: false)]
    public ?string $compositeType = null;

    #[ApiProperty(writable: false)]
    public ?string $compositeStrategy = null;

    #[ApiProperty(writable: false)]
    public ?string $executionStrategy = null;

    #[ApiProperty(writable: false)]
    public array $childDeviceIds = [];

    #[ApiProperty(writable: false)]
    public ?array $nativeGroupInfo = null;

    #[ApiProperty(writable: false)]
    public array $nativeSubGroups = [];

    #[ApiProperty(writable: false)]
    public ?array $nativeSceneInfo = null;

    #[ApiProperty(writable: false)]
    public ?array $sceneStates = null;

    // Virtual device properties
    #[ApiProperty(writable: false)]
    public ?string $virtualType = null;

    #[ApiProperty(writable: false)]
    public ?array $virtualConfig = null;

    // Common properties
    #[ApiProperty(writable: false)]
    public ?string $zoneId = null;

    #[ApiProperty(writable: false)]
    public ?array $metadata = null;

    #[ApiProperty(writable: false)]
    public array $capabilities = [];

    #[ApiProperty(writable: false)]
    public DateTimeInterface $createdAt;

    #[ApiProperty(writable: false)]
    public ?DateTimeInterface $lastSeenAt = null;

    #[ApiProperty(writable: false)]
    public ?DateTimeInterface $lastStateUpdateAt = null;
}
