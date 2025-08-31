<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\CapabilityAction;

use App\Domotic\Domain\Model\CapabilityAction;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityActionResource;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: CapabilityActionResource::class, to: CapabilityAction::class)]
readonly class CapabilityActionResourceToCapabilityActionMapper implements MapperInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    /**
     * @throws MissingModelException
     * @throws ExceptionInterface
     */
    public function load(object $from, string $toClass, array $context): CapabilityAction
    {
        $dto = $from;
        assert($dto instanceof CapabilityActionResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, CapabilityAction::class)) :
            new CapabilityAction()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, CapabilityAction::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): CapabilityAction
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof CapabilityActionResource);
        assert($entity instanceof CapabilityAction);

        $entity
            ->setLabel($dto->label)
            ->setReference($dto->reference)
        ;

        return $entity;
    }
}
