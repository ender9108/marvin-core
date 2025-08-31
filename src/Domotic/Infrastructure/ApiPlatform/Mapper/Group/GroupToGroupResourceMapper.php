<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Group;

use App\Domotic\Domain\Model\Group;
use App\Domotic\Infrastructure\ApiPlatform\Resource\DeviceResource;
use App\Domotic\Infrastructure\ApiPlatform\Resource\GroupResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Group::class, to: GroupResource::class)]
class GroupToGroupResourceMapper implements MapperInterface
{
    public function __construct(
        private readonly MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): GroupResource
    {
        $entity = $from;
        assert($entity instanceof Group);
        $dto = new GroupResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): GroupResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Group);
        assert($dto instanceof GroupResource);

        $dto->name = $entity->getName();
        $dto->slug = $entity->getSlug();
        $dto->devices = $this->microMapper->mapMultiple(
            $entity->getDevices(),
            DeviceResource::class,
            [MicroMapperInterface::MAX_DEPTH => 0]
        );
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $this->translateDto($dto);
    }
}
