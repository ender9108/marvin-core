<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper\User;

use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\Resource\User\CreateUserResource;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: User::class, to: CreateUserResource::class)]
final readonly class EntityToCreateUserResource implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): CreateUserResource
    {
        assert($from instanceof User);

        $dto = new CreateUserResource(
            $from->email->value,
            $from->firstname->value,
            $from->lastname->value,
            $from->roles->toArray(),
            $this->microMapper->map($from->type, UserTypeResource::class, ['MAX_DEPTH' => 0]),
            null, // never expose password
        );

        $dto->id = $from->id->toString();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): CreateUserResource
    {
        assert($from instanceof User);
        assert($to instanceof CreateUserResource);

        return $to;
    }
}
