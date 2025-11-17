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
use Marvin\Device\Application\Command\Device\RegisterBridgeDevice;
use Marvin\Device\Domain\ValueObject\PhysicalAddress;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Device\Domain\ValueObject\TechnicalName;
use Marvin\Device\Presentation\Api\Dto\Input\RegisterBridgeDeviceDto;
use Marvin\Device\Presentation\Api\Resource\ReadDeviceResource;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Symfonycasts\MicroMapper\MicroMapperInterface;

/**
 * Processor for registering a bridge device via API
 */
final readonly class RegisterBridgeDeviceProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param RegisterBridgeDeviceDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        Assert::isInstanceOf($data, RegisterBridgeDeviceDto::class);

        $model = $this->syncCommandBus->handle(new RegisterBridgeDevice(
            label: Label::fromString($data->label),
            protocol: Protocol::from($data->protocol),
            protocolId: new ProtocolId($data->protocolId),
            physicalAddress: PhysicalAddress::fromString($data->physicalAddress),
            technicalName: TechnicalName::fromString($data->technicalName),
            coordinatorInfo: $data->coordinatorInfo,
            networkTopology: $data->networkTopology,
            description: $data->description ? Description::fromString($data->description) : null,
            metadata: $data->metadata ? Metadata::fromArray($data->metadata) : null,
        ));

        // Map Device to ReadDeviceResource (assuming it exists)
        return $this->microMapper->map($model, ReadDeviceResource::class);
    }
}
