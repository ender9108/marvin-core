<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Capability;

use App\Domotic\Domain\Model\Capability;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityResource;
use Psr\Cache\InvalidArgumentException;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: Capability::class, to: CapabilityResource::class)]
class CapabilityToCapabilityResourceMapper implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): CapabilityResource
    {
        $entity = $from;
        assert($entity instanceof Capability);
        $dto = new CapabilityResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function populate(object $from, object $to, array $context): CapabilityResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Capability);
        assert($dto instanceof CapabilityResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
