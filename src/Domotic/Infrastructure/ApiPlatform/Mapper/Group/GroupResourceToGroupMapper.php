<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Group;

use App\Domotic\Domain\Model\Group;
use App\Domotic\Infrastructure\ApiPlatform\Resource\GroupResource;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: GroupResource::class, to: Group::class)]
readonly class GroupResourceToGroupMapper implements MapperInterface
{
    public function __construct(
        private QueryBus $queryBus,
        private MicroMapperInterface $microMapper,
    ) {
    }

    /**
     * @throws MissingModelException
     * @throws ExceptionInterface
     */
    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof GroupResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, Group::class)) :
            new Group()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, Group::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof GroupResource);
        assert($entity instanceof Group);

        $entity->setName($dto->name);
        $entity->setSlug($dto->slug);
        $entity->setDevices($dto->devices);

        return $entity;
    }
}
