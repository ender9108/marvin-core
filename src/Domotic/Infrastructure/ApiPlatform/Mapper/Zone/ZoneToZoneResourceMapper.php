<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Zone;

use App\Domotic\Domain\Model\Zone;
use App\Domotic\Infrastructure\ApiPlatform\Resource\DeviceResource;
use App\Domotic\Infrastructure\ApiPlatform\Resource\ZoneResource;
use Psr\Cache\InvalidArgumentException;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Zone::class, to: ZoneResource::class)]
readonly class ZoneToZoneResourceMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Zone);
        $dto = new ZoneResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Zone);
        assert($dto instanceof ZoneResource);

        $dto->label = $entity->getLabel();
        $dto->area = $entity->getArea();
        $dto->devices = $this->microMapper->mapMultiple(
            $entity->getDevices(),
            DeviceResource::class,
            [MicroMapperInterface::MAX_DEPTH => 0]
        );
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
