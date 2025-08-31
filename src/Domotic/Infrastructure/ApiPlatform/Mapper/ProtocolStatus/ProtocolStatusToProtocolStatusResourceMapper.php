<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\ProtocolStatus;

use App\Domotic\Domain\Model\ProtocolStatus;
use App\Domotic\Infrastructure\ApiPlatform\Resource\ProtocolStatusResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: ProtocolStatus::class, to: ProtocolStatusResource::class)]
class ProtocolStatusToProtocolStatusResourceMapper  implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): ProtocolStatusResource
    {
        $entity = $from;
        assert($entity instanceof ProtocolStatus);
        $dto = new ProtocolStatusResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): ProtocolStatusResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof ProtocolStatus);
        assert($dto instanceof ProtocolStatusResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
