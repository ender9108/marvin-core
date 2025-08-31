<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\CapabilityState;

use App\Domotic\Domain\Model\CapabilityState;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityStateResource;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: CapabilityStateResource::class, to: CapabilityState::class)]
readonly class CapabilityStateResourceToCapabilityStateMapper implements MapperInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    /**
     * @throws MissingModelException
     * @throws ExceptionInterface
     */
    public function load(object $from, string $toClass, array $context): CapabilityState
    {
        $dto = $from;
        assert($dto instanceof CapabilityStateResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, CapabilityState::class)) :
            new CapabilityState()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, CapabilityState::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): CapabilityState
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof CapabilityStateResource);
        assert($entity instanceof CapabilityState);

        $entity
            ->setLabel($dto->label)
            ->setReference($dto->reference)
            ->setSchema($dto->schema)
        ;

        return $entity;
    }
}
