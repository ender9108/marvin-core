<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper\User;

use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\Resource\User\UserResource;
use Marvin\Security\Presentation\Api\Resource\UserStatus\UserStatusResource;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: User::class, to: UserResource::class)]
readonly class EntityToUserResource implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): UserResource
    {
        assert($from instanceof User);

        $dto = new UserResource(
            $from->email->value,
            $from->firstname->value,
            $from->lastname->value,
            $from->roles->toArray(),
            $from->locale->value,
            $from->theme->value,
            $this->microMapper->map($from->type, UserTypeResource::class, ['MAX_DEPTH' => 0]),
        );

        $dto->id = $from->id->toString();
        $dto->status = $this->microMapper->map($from->status, UserStatusResource::class, ['MAX_DEPTH' => 0]);
        $dto->createdAt = $from->createdAt->value;
        $dto->updatedAt = $from->updatedAt->value;

        return $dto;
    }

    public function populate(object $from, object $to, array $context): UserResource
    {
        assert($from instanceof User);
        assert($to instanceof UserResource);

        return $to;
    }
}
