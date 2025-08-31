<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\CapabilityAction;

use App\Domotic\Domain\Model\CapabilityAction;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityActionResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: CapabilityAction::class, to: CapabilityActionResource::class)]
class CapabilityActionToCapabilityActionResourceMapper implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): CapabilityActionResource
    {
        $entity = $from;
        assert($entity instanceof CapabilityAction);
        $dto = new CapabilityActionResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): CapabilityActionResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof CapabilityAction);
        assert($dto instanceof CapabilityActionResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
