<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\CapabilityComposition;

use App\Domotic\Domain\Model\Capability;
use App\Domotic\Domain\Model\CapabilityAction;
use App\Domotic\Domain\Model\CapabilityComposition;
use App\Domotic\Domain\Model\CapabilityState;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityCompositionResource;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: CapabilityCompositionResource::class, to: CapabilityComposition::class)]
readonly class CapabilityCompositionResourceToCapabilityCompositionMapper implements MapperInterface
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
    public function load(object $from, string $toClass, array $context): CapabilityComposition
    {
        $dto = $from;
        assert($dto instanceof CapabilityCompositionResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, CapabilityComposition::class)) :
            new CapabilityComposition()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, CapabilityComposition::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): CapabilityComposition
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof CapabilityCompositionResource);
        assert($entity instanceof CapabilityComposition);

        $entity
            ->setCapability($this->microMapper->map(
                $dto->capability,
                Capability::class,
                [MicroMapperInterface::MAX_DEPTH => 0]
            ))
        ;

        foreach ($dto->capabilityActions as $capabilityAction) {
            $entity->addCapabilityAction($this->microMapper->map(
                $capabilityAction,
                CapabilityAction::class,
                [MicroMapperInterface::MAX_DEPTH => 0]
            ));
        }

        foreach ($dto->capabilityStates as $capabilityState) {
            $entity->addCapabilityState($this->microMapper->map(
                $capabilityState,
                CapabilityState::class,
                [MicroMapperInterface::MAX_DEPTH => 0]
            ));
        }

        return $entity;
    }
}
