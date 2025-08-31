<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Device;

use App\Domotic\Domain\Model\Device;
use App\Domotic\Infrastructure\ApiPlatform\Resource\DeviceResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Device::class, to: DeviceResource::class)]
class DeviceToDeviceResourceMapper implements MapperInterface
{
    public function __construct(
        private readonly MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Device);
        $dto = new DeviceResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Device);
        assert($dto instanceof DeviceResource);

        $dto->name = $entity->getName();
        $dto->technicalName = $entity->getTechnicalName();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
