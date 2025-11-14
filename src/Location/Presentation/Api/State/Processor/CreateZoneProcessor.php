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
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Humidity;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Location\Presentation\Api\Dto\Input\AddDeviceToZoneDto;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Location\Application\Command\Zone\AddDeviceToZone;
use Marvin\Location\Presentation\Api\Resource\ReadZoneResource;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class CreateZoneProcessor implements ProcessorInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param AddDeviceToZoneDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadZoneResource
    {
        Assert::isInstanceOf($data, AddDeviceToZoneDto::class);

        $model = $this->syncCommandBus->handle(new CreateZone(
            ZoneName::fromString($data->name),
            ZoneType::from($data->type),
            null !== $data->parentZoneId ? new ZoneId($data->parentZoneId) : null,
            null !== $data->surfaceArea ? SurfaceArea::fromFloat($data->surfaceArea) : null,
            null !== $data->orientation ? Orientation::from($data->orientation) : null,
            null !== $data->targetTemperature ? Temperature::fromCelsius($data->targetTemperature) : null,
            null !== $data->targetHumidity ? Humidity::fromPercentage($data->targetHumidity) : null,
            null !== $data->targetPowerConsumption ? PowerConsumption::fromWatts($data->targetPowerConsumption) : null,
            $data->icon,
            null !== $data->color ? HexaColor::fromString($data->color) : null,
            null !== $data->metadata ? $data->metadata : null,
        ));

        /*
         * public ZoneName $zoneName,
        public ZoneType $type,
        public ?ZoneId $parentZoneId = null,
        public ?SurfaceArea $surfaceArea = null,
        public ?Orientation $orientation = null,
        public ?Temperature $targetTemperature = null,
        public ?Humidity $targetHumidity = null,
        public ?PowerConsumption $targetPowerConsumption = null,
        public ?string $icon = null,
        public ?HexaColor $color = null,
        public ?Metadata $metadata = null,
         */

        return $this->objectMapper->map($model, ReadZoneResource::class);
    }
}
