<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\CapabilityComposition;

use App\Domotic\Domain\Model\CapabilityComposition;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityActionResource;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityCompositionResource;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityResource;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityStateResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: CapabilityComposition::class, to: CapabilityCompositionResource::class)]
readonly class CapabilityCompositionToCapabilityCompositionResourceMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): CapabilityCompositionResource
    {
        $entity = $from;
        assert($entity instanceof CapabilityComposition);
        $dto = new CapabilityCompositionResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): CapabilityCompositionResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof CapabilityComposition);
        assert($dto instanceof CapabilityCompositionResource);

        $dto->capability = $this->microMapper->map(
            $entity->getCapability(),
            CapabilityResource::class,
            [MicroMapperInterface::MAX_DEPTH => 0]
        );
        $dto->capabilityActions = $this->microMapper->mapMultiple(
            $entity->getCapabilityActions(),
            CapabilityActionResource::class,
            [MicroMapperInterface::MAX_DEPTH => 0]
        );
        $dto->capabilityStates = $this->microMapper->mapMultiple(
            $entity->getCapabilityStates(),
            CapabilityStateResource::class,
            [MicroMapperInterface::MAX_DEPTH => 0]
        );
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
