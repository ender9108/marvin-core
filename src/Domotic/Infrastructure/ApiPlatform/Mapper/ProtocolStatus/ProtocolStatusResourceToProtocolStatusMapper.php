<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\ProtocolStatus;

use App\Domotic\Domain\Model\ProtocolStatus;
use App\Domotic\Infrastructure\ApiPlatform\Resource\ProtocolStatusResource;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: ProtocolStatusResource::class, to: ProtocolStatus::class)]
readonly class ProtocolStatusResourceToProtocolStatusMapper implements MapperInterface
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
        assert($dto instanceof ProtocolStatusResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, ProtocolStatus::class)) :
            new ProtocolStatus()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, ProtocolStatus::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof ProtocolStatusResource);
        assert($entity instanceof ProtocolStatus);

        $entity->setLabel($dto->label);
        $entity->setReference($dto->reference);

        return $entity;
    }
}
