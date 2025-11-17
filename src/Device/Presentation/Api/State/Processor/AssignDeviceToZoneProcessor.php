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

namespace Marvin\Device\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Device\Application\Command\Device\AssignDeviceToZone;
use Marvin\Device\Presentation\Api\Dto\Input\AssignDeviceToZoneDto;
use Marvin\Device\Presentation\Api\Resource\ReadDeviceResource;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class AssignDeviceToZoneProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param AssignDeviceToZoneDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadDeviceResource
    {
        Assert::isInstanceOf($data, AssignDeviceToZoneDto::class);
        Assert::keyExists($uriVariables, 'id');

        $model = $this->syncCommandBus->handle(new AssignDeviceToZone(
            DeviceId::fromString($uriVariables['id']),
            new ZoneId($data->zoneId),
        ));

        return $this->microMapper->map($model, ReadDeviceResource::class);
    }
}
