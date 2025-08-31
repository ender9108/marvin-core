<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\User;

use App\System\Domain\Model\User;
use App\System\Infrastructure\ApiPlatform\Resource\UserResource;
use App\System\Infrastructure\ApiPlatform\Resource\UserStatusResource;
use App\System\Infrastructure\ApiPlatform\Resource\UserTypeResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: User::class, to: UserResource::class)]
readonly class UserToUserResourceMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): UserResource
    {
        $entity = $from;
        assert($entity instanceof User);

        $dto = new UserResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): UserResource
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof User);
        assert($dto instanceof UserResource);

        $dto->firstName = $entity->getFirstName();
        $dto->lastName = $entity->getLastName();
        $dto->email = $entity->getEmail();
        $dto->roles = $entity->getRoles();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();
        $dto->status = $this->microMapper->map($entity->getStatus(), UserStatusResource::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]);
        $dto->type = $this->microMapper->map($entity->getType(), UserTypeResource::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]);

        return $dto;
    }
}
