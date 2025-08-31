<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Protocol;

use App\Domotic\Domain\Model\Protocol;
use App\Domotic\Infrastructure\ApiPlatform\Resource\ProtocolResource;
use App\Domotic\Infrastructure\ApiPlatform\Resource\ProtocolStatusResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Protocol::class, to: ProtocolResource::class)]
class ProtocolToProtocolResourceMapper implements MapperInterface
{
    public function __construct(
        private readonly MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): ProtocolResource
    {
        $entity = $from;
        assert($entity instanceof Protocol);
        $dto = new ProtocolResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): ProtocolResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Protocol);
        assert($dto instanceof ProtocolResource);

        /* Add your mapping here */
        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->status = $this->microMapper->map(
            $entity->getStatus(),
            ProtocolStatusResource::class,
            [MicroMapperInterface::MAX_DEPTH => 0]
        );
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
