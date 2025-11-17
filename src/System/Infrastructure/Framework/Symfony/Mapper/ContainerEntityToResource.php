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

namespace Marvin\System\Infrastructure\Framework\Symfony\Mapper;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Marvin\System\Domain\Model\Container;
use Marvin\System\Presentation\Api\Resource\ReadContainerResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: Container::class, to: ReadContainerResource::class)]
final readonly class ContainerEntityToResource implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): object
    {
        /** @var Container $entity */
        $entity = $from;

        Assert::isInstanceOf($entity, Container::class);

        $resource = new ReadContainerResource();

        $resource->id = $entity->id->toString();

        return $resource;
    }

    public function populate(object $from, object $to, array $context): object
    {
        /** @var Container $entity */
        $entity = $from;
        /** @var ReadContainerResource $resource */
        $resource = $to;

        Assert::isInstanceOf($entity, Container::class);
        Assert::isInstanceOf($resource, ReadContainerResource::class);

        $resource->type = $entity->type->value;
        $resource->uptime = $entity->status->value;
        $resource->serviceLabel = $entity->serviceLabel->value;
        $resource->containerLabel = $entity->containerLabel;
        $resource->createdAt = $entity->createdAt;
        $resource->lastSyncedAt = $entity->lastSyncedAt;

        return $resource;
    }
}
