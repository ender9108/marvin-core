<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\UserType;

use App\System\Domain\Model\UserType;
use App\System\Infrastructure\ApiPlatform\Resource\UserTypeResource;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: UserTypeResource::class, to: UserType::class)]
readonly class UserTypeResourceToEntityMapper implements MapperInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws MissingModelException
     */
    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof UserTypeResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, UserType::class)) :
            new UserType()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, UserType::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof UserTypeResource);
        assert($entity instanceof UserType);

        $entity->setLabel($to->label);
        $entity->setReference($to->reference);

        return $entity;
    }
}
