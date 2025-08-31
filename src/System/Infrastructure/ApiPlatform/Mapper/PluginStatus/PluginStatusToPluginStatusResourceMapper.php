<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\PluginStatus;

use App\System\Domain\Model\PluginStatus;
use App\System\Infrastructure\ApiPlatform\Resource\PluginStatusResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: PluginStatus::class, to: PluginStatusResource::class)]
class PluginStatusToPluginStatusResourceMapper implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): PluginStatusResource
    {
        $entity = $from;
        assert($entity instanceof PluginStatus);
        $dto = new PluginStatusResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): PluginStatusResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof PluginStatus);
        assert($dto instanceof PluginStatusResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
