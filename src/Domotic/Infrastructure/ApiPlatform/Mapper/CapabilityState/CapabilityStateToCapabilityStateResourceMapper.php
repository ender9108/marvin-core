<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\CapabilityState;

use App\Domotic\Domain\Model\CapabilityState;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityStateResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: CapabilityState::class, to: CapabilityStateResource::class)]
class CapabilityStateToCapabilityStateResourceMapper implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): CapabilityStateResource
    {
        $entity = $from;
        assert($entity instanceof CapabilityState);
        $dto = new CapabilityStateResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): CapabilityStateResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof CapabilityState);
        assert($dto instanceof CapabilityStateResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->schema = $entity->getSchema();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
