<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper\User;

use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\Model\UserType;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Presentation\Api\Resource\User\UserResource;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: UserResource::class, to: User::class)]
readonly class UserResourceToEntity implements MapperInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function load(object $from, string $toClass, array $context): User
    {
        assert($from instanceof UserResource);

        $entity = $from->id ?
            $this->queryBus->handle(new FindItemQuery($from->id, User::class)) :
            User::create(
                new Email($from->email),
                new Firstname($from->firstname),
                new Lastname($from->lastname),
                $this->microMapper->map($from->status, UserStatus::class, ['MAX_DEPTH' => 0]),
                $this->microMapper->map($from->type, UserType::class, ['MAX_DEPTH' => 0]),
                new Roles($from->roles),
                new Locale($from->locale),
                new Theme($from->theme),
            )
        ;

        if (!$entity) {
            throw UserNotFound::withId(new UserId($from->id));
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): User
    {
        assert($from instanceof UserResource);
        assert($to instanceof User);

        return $to;
    }
}
