<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper\UserStatus;

use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Marvin\Security\Domain\Exception\UserStatusNotFound;
use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Domain\ValueObject\Identity\UserStatusId;
use Marvin\Security\Presentation\Api\Resource\UserStatus\UserStatusResource;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: UserStatusResource::class, to: UserStatus::class)]
readonly class ResourceToEntity implements MapperInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    /**
     * @throws MissingModelException
     */
    public function load(object $from, string $toClass, array $context): UserStatus
    {
        assert($from instanceof UserStatusResource);

        $entity = $from->id ?
            $this->queryBus->handle(new FindItemQuery($from->id, UserStatus::class)) :
            new UserStatus(
                new Label($from->label),
                new Reference($from->reference),
            )
        ;

        if (!$entity) {
            throw UserStatusNotFound::withId(new UserStatusId($from->id));
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): UserStatus
    {
        assert($from instanceof UserStatusResource);
        assert($to instanceof UserStatus);

        return $to;
    }
}
