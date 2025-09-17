<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper\User;

use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Presentation\Api\Resource\User\CreateUserResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: CreateUserResource::class, to: User::class)]
readonly class CreateUserResourceToEntity implements MapperInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    public function load(object $from, string $toClass, array $context): User
    {
        assert($from instanceof CreateUserResource);

        $entity = $this->queryBus->handle(new FindItemQuery($from->id, User::class));

        if (!$entity) {
            throw UserNotFound::withId(new UserId($from->id));
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): User
    {
        assert($from instanceof CreateUserResource);
        assert($to instanceof User);

        return $to;
    }
}
