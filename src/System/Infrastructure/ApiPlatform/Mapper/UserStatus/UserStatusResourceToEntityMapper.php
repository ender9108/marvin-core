<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\UserStatus;

use App\System\Domain\Model\UserStatus;
use App\System\Infrastructure\ApiPlatform\Resource\UserStatusResource;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: UserStatusResource::class, to: UserStatus::class)]
readonly class UserStatusResourceToEntityMapper implements MapperInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    /**
     * @throws MissingModelException
     * @throws ExceptionInterface
     */
    public function load(object $from, string $toClass, array $context): UserStatus
    {
        $dto = $from;
        assert($dto instanceof UserStatusResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, UserStatus::class)) :
            new UserStatus()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, UserStatus::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): UserStatus
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof UserStatusResource);
        assert($entity instanceof UserStatus);

        $entity->setLabel($to->label);
        $entity->setReference($to->reference);

        return $entity;
    }
}
