<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Capability;

use App\Domotic\Domain\Model\Capability;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityResource;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: CapabilityResource::class, to: Capability::class)]
readonly class CapabilityResourceToCapabilityMapper implements MapperInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    /**
     * @throws MissingModelException
     * @throws ExceptionInterface
     */
    public function load(object $from, string $toClass, array $context): Capability
    {
        $dto = $from;
        assert($dto instanceof CapabilityResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, Capability::class)) :
            new Capability()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, Capability::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): Capability
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof CapabilityResource);
        assert($entity instanceof Capability);

        $entity
            ->setLabel($dto->label)
            ->setReference($dto->reference)
        ;

        return $entity;
    }
}
