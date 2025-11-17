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
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Device\Application\Command\Device\CreatePhysicalDevice;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\PhysicalAddress;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Device\Domain\ValueObject\TechnicalName;
use Marvin\Device\Presentation\Api\Dto\Input\CreatePhysicalDeviceDto;
use Marvin\Device\Presentation\Api\Resource\ReadDeviceResource;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class CreatePhysicalDeviceProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private CommandBusInterface $commandBus,
    ) {
    }

    /**
     * @param CreatePhysicalDeviceDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadDeviceResource
    {
        Assert::isInstanceOf($data, CreatePhysicalDeviceDto::class);

        $model = $this->commandBus->dispatch(new CreatePhysicalDevice(
            Label::fromString($data->label),
            DeviceType::from($data->deviceType),
            Protocol::from($data->protocol),
            new ProtocolId($data->protocolId),
            PhysicalAddress::fromString($data->physicalAddress),
            TechnicalName::fromString($data->technicalName),
            $data->capabilities,
            null !== $data->zoneId ? new ZoneId($data->zoneId) : null,
            null !== $data->description ? Description::fromString($data->description) : null,
            null !== $data->metadata ? Metadata::fromArray($data->metadata) : null,
        ));

        return $this->microMapper->map($model, ReadDeviceResource::class);
    }
}
