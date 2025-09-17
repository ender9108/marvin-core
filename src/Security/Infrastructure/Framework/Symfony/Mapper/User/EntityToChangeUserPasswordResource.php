<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper\User;

use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\Resource\User\ChangeUserPasswordResource;
use Marvin\Security\Presentation\Api\Resource\User\GetUserResource;
use Marvin\Security\Presentation\Api\Resource\UserStatus\UserStatusResource;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: User::class, to: ChangeUserPasswordResource::class)]
class EntityToChangeUserPasswordResource implements MapperInterface
{
    public function __construct(
        private readonly MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): GetUserResource
    {
        assert($from instanceof User);

        $dto = new GetUserResource(
            $from->email->value,
            $from->firstname->value,
            $from->lastname->value,
            $from->roles->toArray(),
            $this->microMapper->map($from->type, UserTypeResource::class, ['MAX_DEPTH' => 0]),
            $this->microMapper->map($from->status, UserStatusResource::class, ['MAX_DEPTH' => 0]),
            $from->createdAt->value,
            $from->updatedAt->value,
        );

        $dto->id = $from->id->toString();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): GetUserResource
    {
        assert($from instanceof User);
        assert($to instanceof GetUserResource);

        return $to;
    }
}
