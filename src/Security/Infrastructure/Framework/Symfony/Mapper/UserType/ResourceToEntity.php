<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper\UserType;

use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Marvin\Security\Domain\Exception\UserTypeNotFound;
use Marvin\Security\Domain\Model\UserType;
use Marvin\Security\Domain\ValueObject\Identity\UserTypeId;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: UserTypeResource::class, to: UserType::class)]
readonly class ResourceToEntity implements MapperInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    /**
     * @throws MissingModelException
     */
    public function load(object $from, string $toClass, array $context): UserType
    {
        $dto = $from;
        assert($dto instanceof UserTypeResource);

        $entity = $dto->id ?
            $this->queryBus->handle(new FindItemQuery($dto->id, UserType::class)) :
            new UserType(
                new Label($dto->label),
                new Reference($dto->reference),
            )
        ;

        if (!$entity) {
            throw UserTypeNotFound::withId(new UserTypeId($dto->id));
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): UserType
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof UserTypeResource);
        assert($entity instanceof UserType);

        return $entity;
    }
}
