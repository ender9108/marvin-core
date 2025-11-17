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

namespace Marvin\Device\Infrastructure\Framework\Symfony\Mapper;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Model\DeviceCapability;
use Marvin\Device\Presentation\Api\Resource\ReadDeviceResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: Device::class, to: ReadDeviceResource::class)]
final class DeviceEntityToResource implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): object
    {
        /** @var Device $entity */
        $entity = $from;

        Assert::isInstanceOf($entity, Device::class);

        $resource = new ReadDeviceResource();

        $resource->id = $entity->id->toString();

        return $resource;
    }

    public function populate(object $from, object $to, array $context): object
    {
        /** @var Device $entity */
        $entity = $from;
        /** @var ReadDeviceResource $resource */
        $resource = $to;

        Assert::isInstanceOf($entity, Device::class);
        Assert::isInstanceOf($resource, ReadDeviceResource::class);

        $resource->label = $entity->label->value;
        $resource->description = $entity->description?->value;
        $resource->deviceType = $entity->deviceType->value;
        $resource->status = $entity->status->value;

        // Physical device properties
        $resource->protocol = $entity->protocol?->value;
        $resource->protocolId = $entity->protocolId?->toString();
        $resource->physicalAddress = $entity->physicalAddress?->value;
        $resource->technicalName = $entity->technicalName?->value;

        // Composite device properties
        $resource->compositeType = $entity->compositeType?->value;
        $resource->compositeStrategy = $entity->compositeStrategy?->value;
        $resource->executionStrategy = $entity->executionStrategy?->value;
        $resource->childDeviceIds = array_map(
            fn ($id) => $id->toString(),
            $entity->childDeviceIds
        );
        $resource->nativeGroupInfo = $entity->nativeGroupInfo?->toArray();
        $resource->nativeSubGroups = $entity->nativeSubGroups;
        $resource->nativeSceneInfo = $entity->nativeSceneInfo?->toArray();
        $resource->sceneStates = $entity->sceneStates?->toArray();

        // Virtual device properties
        $resource->virtualType = $entity->virtualType?->value;
        $resource->virtualConfig = $entity->virtualConfig?->toArray();

        // Common properties
        $resource->zoneId = $entity->zoneId?->toString();
        $resource->metadata = $entity->metadata?->toArray();

        // Capabilities
        $resource->capabilities = array_map(
            fn (DeviceCapability $capability) => [
                'id' => $capability->id->toString(),
                'capability' => $capability->capability->value,
                'stateName' => $capability->stateName,
                'state' => $capability->state?->toArray(),
                'unit' => $capability->unit,
                'metadata' => $capability->metadata?->toArray(),
            ],
            $entity->capabilities->toArray()
        );

        $resource->createdAt = $entity->createdAt;
        $resource->lastSeenAt = $entity->lastSeenAt;
        $resource->lastStateUpdateAt = $entity->lastStateUpdateAt;

        return $resource;
    }
}
