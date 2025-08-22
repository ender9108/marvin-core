<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Protocol;

use App\Domotic\Domain\Model\Protocol;
use App\Domotic\Infrastructure\ApiPlatform\Resource\ProtocolResource;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: ProtocolResource::class, to: Protocol::class)]
readonly class ProtocolResourceToProtocolMapper implements MapperInterface
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
        assert($dto instanceof ProtocolResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, Protocol::class)) :
            new Protocol()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, Protocol::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof ProtocolResource);
        assert($entity instanceof Protocol);

        $entity->setLabel($dto->label);
        $entity->setReference($dto->reference);

        return $entity;
    }
}
