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

namespace Marvin\Location\Infrastructure\Framework\Symfony\Mapper;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Presentation\Api\Resource\ReadZoneResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Zone::class, to: ReadZoneResource::class)]
final readonly class ZoneEntityToResource implements MapperInterface
{
    public function __construct(private MicroMapperInterface $microMapper) {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        /** @var Zone $entity */
        $entity = $from;

        Assert::isInstanceOf($entity, Zone::class);

        $resource = new ReadZoneResource();

        $resource->id = $entity->id->toString();

        return $resource;
    }

    public function populate(object $from, object $to, array $context): object
    {
        /** @var Zone $entity */
        $entity = $from;
        /** @var ReadZoneResource $resource */
        $resource = $to;

        Assert::isInstanceOf($entity, Zone::class);
        Assert::isInstanceOf($resource, ReadZoneResource::class);

        $resource->zoneName = $entity->zoneName->value;
        $resource->type = $entity->type->value;
        $resource->targetTemperature = $entity->targetTemperature?->value;
        $resource->targetPowerConsumption = $entity->targetPowerConsumption?->value;
        $resource->targetHumidity = $entity->targetHumidity?->value;
        $resource->icon = $entity->icon;
        $resource->surfaceArea = $entity->surfaceArea?->value;
        $resource->orientation = $entity->orientation?->value;
        $resource->color = $entity->color?->value;
        $resource->metadata = $entity->metadata?->toArray();
        $resource->updatedAt = $entity->updatedAt;
        $resource->createdAt = $entity->createdAt;

        $resource->currentTemperature = $entity->currentTemperature?->value;
        $resource->currentPowerConsumption = $entity->currentPowerConsumption?->value;
        $resource->currentHumidity = $entity->currentHumidity?->value;
        $resource->isOccupied = $entity->isOccupied;
        $resource->lastMetricsUpdate = $entity->lastMetricsUpdate;

        $resource->deviceIds = $entity->deviceIds;

        if ($entity->parent instanceof Zone) {
            $resource->parent = $this->microMapper->map($entity->parent, ReadZoneResource::class, [
                MicroMapperInterface::MAX_DEPTH => 0,
            ]);
        }

        /** @var Zone $children */
        foreach ($entity->childrens as $children) {
            $resource->childrens[] = $children->id->toString();
        }

        return $resource;
    }
}
