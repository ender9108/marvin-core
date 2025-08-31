<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\UserStatus;

use App\System\Domain\Model\UserStatus;
use App\System\Infrastructure\ApiPlatform\Resource\UserStatusResource;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use Psr\Cache\InvalidArgumentException;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: UserStatus::class, to: UserStatusResource::class)]
class UserStatusToResourceMapper implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): UserStatusResource
    {
        $entity = $from;
        assert($entity instanceof UserStatus);

        $dto = new UserStatusResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): UserStatusResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof UserStatus);
        assert($dto instanceof UserStatusResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $dto;
    }
}
