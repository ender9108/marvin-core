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
use Marvin\Device\Application\Command\Group\CreateGroup;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\ExecutionStrategy;
use Marvin\Device\Presentation\Api\Dto\Input\CreateGroupDto;
use Marvin\Device\Presentation\Api\Resource\ReadDeviceResource;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class CreateGroupProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param CreateGroupDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadDeviceResource
    {
        Assert::isInstanceOf($data, CreateGroupDto::class);

        $childrenDeviceIds = array_map(
            fn (string $id) => new DeviceId($id),
            $data->childrenDeviceIds
        );

        $model = $this->syncCommandBus->handle(new CreateGroup(
            Label::fromString($data->label),
            $childrenDeviceIds,
            $data->capabilities,
            CompositeStrategy::from($data->compositeStrategy),
            ExecutionStrategy::from($data->executionStrategy),
            null !== $data->zoneId ? new ZoneId($data->zoneId) : null,
            null !== $data->description ? Description::fromString($data->description) : null,
            null !== $data->metadata ? Metadata::fromArray($data->metadata) : null,
        ));

        return $this->microMapper->map($model, ReadDeviceResource::class);
    }
}
