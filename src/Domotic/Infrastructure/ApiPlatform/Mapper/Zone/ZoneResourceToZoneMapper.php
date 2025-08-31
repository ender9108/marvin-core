<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Zone;

use App\Domotic\Domain\Model\Device;
use App\Domotic\Domain\Model\Zone;
use App\Domotic\Infrastructure\ApiPlatform\Resource\ZoneResource;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: ZoneResource::class, to: Zone::class)]
readonly class ZoneResourceToZoneMapper implements MapperInterface
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
        assert($dto instanceof ZoneResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, Zone::class)) :
            new Zone()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, Zone::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof ZoneResource);
        assert($entity instanceof Zone);

        $entity->setLabel($dto->label);
        $entity->setArea($dto->area);

        foreach ($dto->devices as $deviceResource) {
            $entity->addDevice($this->microMapper->map(
                $deviceResource,
                Device::class,
                [MicroMapperInterface::MAX_DEPTH => 0]
            ));
        }

        return $entity;
    }
}
