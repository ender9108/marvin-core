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
use Marvin\Location\Application\Command\Zone\AddDeviceToZone;
use Marvin\Location\Presentation\Api\Dto\Input\AddDeviceToZoneDto;
use Marvin\Location\Presentation\Api\Resource\ReadZoneResource;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class AddDeviceToZoneProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
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

        $zone = $this->syncCommandBus->handle(new AddDeviceToZone(
            new ZoneId($uriVariables['id']),
            new DeviceId($data->deviceId),
        ));

        return $this->microMapper->map($zone, ReadZoneResource::class);
    }
}
