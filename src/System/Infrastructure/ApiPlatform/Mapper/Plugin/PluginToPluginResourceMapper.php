<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\Plugin;

use App\System\Domain\Model\Plugin;
use App\System\Infrastructure\ApiPlatform\Resource\PluginResource;
use App\System\Infrastructure\ApiPlatform\Resource\PluginStatusResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Plugin::class, to: PluginResource::class)]
readonly class PluginToPluginResourceMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper
    ) {
    }

    public function load(object $from, string $toClass, array $context): PluginResource
    {
        $entity = $from;
        assert($entity instanceof Plugin);
        $dto = new PluginResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): PluginResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Plugin);
        assert($dto instanceof PluginResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->description = $entity->getDescription();
        $dto->version = $entity->getVersion();
        $dto->metadata = $entity->getMetadata();
        $dto->status = $this->microMapper->map(
            $entity->getStatus(),
            PluginStatusResource::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]);
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
