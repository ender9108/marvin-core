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

namespace Marvin\Location\Presentation\Api\Resource;

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
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Presentation\Api\Dto\Input\AddDeviceToZoneDto;
use Marvin\Location\Presentation\Api\Dto\Input\CreateZoneDto;
use Marvin\Location\Presentation\Api\Dto\Input\MoveToZoneDto;
use Marvin\Location\Presentation\Api\Dto\Input\UpdateZoneDto;
use Marvin\Location\Presentation\Api\State\Processor\AddDeviceToZoneProcessor;
use Marvin\Location\Presentation\Api\State\Processor\CreateZoneProcessor;
use Marvin\Location\Presentation\Api\State\Processor\DeleteZoneProcessor;
use Marvin\Location\Presentation\Api\State\Processor\MoveToZoneProcessor;
use Marvin\Location\Presentation\Api\State\Processor\UpdateZoneProcessor;

#[ApiResource(
    shortName: 'zone',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            input: CreateZoneDto::class,
            processor: CreateZoneProcessor::class
        ),
        new Post(
            uriTemplate: '/zones/{id}/add-device-to-zone',
            input: AddDeviceToZoneDto::class,
            processor: AddDeviceToZoneProcessor::class
        ),
        new Post(
            uriTemplate: '/zones/{id}/move-to-zone',
            input: MoveToZoneDto::class,
            processor: MoveToZoneProcessor::class
        ),
        new Patch(
            input: UpdateZoneDto::class,
            processor: UpdateZoneProcessor::class
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
            processor: DeleteZoneProcessor::class
        ),
    ],
    routePrefix: '/location',
    normalizationContext: ['skip_null_values' => false],
    provider: EntityToApiStateProvider::class,
    stateOptions: new Options(entityClass: Zone::class),
)]
class ReadZoneResource
{
    #[ApiProperty(writable: false, identifier: true)]
    public string $id;

    #[ApiProperty(writable: false)]
    public ?string $zoneName = null;

    #[ApiProperty(writable: false)]
    public string $slug;

    #[ApiProperty(writable: false)]
    public string $type;

    #[ApiProperty(writable: false)]
    public ?string $orientation = null;

    #[ApiProperty(writable: false)]
    public ?float $surfaceArea = null;

    #[ApiProperty(writable: false)]
    public ?string $icon = null;

    #[ApiProperty(writable: false)]
    public ?string $color = null;

    #[ApiProperty(writable: false)]
    public ?float $targetTemperature = null;

    #[ApiProperty(writable: false)]
    public ?float $targetPowerConsumption = null;

    #[ApiProperty(writable: false)]
    public ?float $targetHumidity = null;

    #[ApiProperty(writable: false)]
    public ?float $currentTemperature = null;

    #[ApiProperty(writable: false)]
    public ?float $currentPowerConsumption = null;

    #[ApiProperty(writable: false)]
    public ?float $currentHumidity = null;

    #[ApiProperty(writable: false)]
    public bool $isOccupied;

    #[ApiProperty(writable: false)]
    public ?array $metadata = null;

    #[ApiProperty(writable: false)]
    public ?ReadZoneResource $parent = null;

    #[ApiProperty(writable: false)]
    public array $childrens = [];

    #[ApiProperty(writable: false)]
    public array $deviceIds = [];

    #[ApiProperty(writable: false)]
    public array $deviceTemperatures = [];

    #[ApiProperty(writable: false)]
    public array $deviceHumidities = [];

    #[ApiProperty(writable: false)]
    public array $devicePowerConsumptions = [];

    #[ApiProperty(writable: false)]
    public ?DateTimeInterface $lastMetricsUpdate = null;

    #[ApiProperty(writable: false)]
    public ?DateTimeInterface $updatedAt = null;

    #[ApiProperty(writable: false)]
    public DateTimeInterface $createdAt;
}
