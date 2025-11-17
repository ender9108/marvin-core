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

namespace Marvin\Location\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Location\Application\Command\Zone\UpdateZone;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Humidity;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Location\Presentation\Api\Dto\Input\UpdateZoneDto;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Metadata;

final readonly class UpdateZoneProcessor implements ProcessorInterface
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param UpdateZoneDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, UpdateZoneDto::class);

        $this->syncCommandBus->handle(
            new UpdateZone(
                ZoneId::fromString($uriVariables['id']),
                null !== $data->zoneName ? ZoneName::fromString($data->zoneName) : null,
                null !== $data->surfaceArea ? SurfaceArea::fromFloat($data->surfaceArea) : null,
                null !== $data->orientation ? Orientation::from($data->orientation) : null,
                null !== $data->targetTemperature ? Temperature::fromCelsius($data->targetTemperature) : null,
                null !== $data->targetHumidity ? Humidity::fromPercentage($data->targetHumidity) : null,
                null !== $data->targetPowerConsumption ? PowerConsumption::fromWatts($data->targetPowerConsumption) : null,
                $data->icon ?? null,
                null !== $data->color ? HexaColor::fromString($data->color) : null,
                null !== $data->metadata ? Metadata::fromArray($data->metadata) : null,
            )
        );

        return $data;
    }
}
