<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Device;

use App\Domotic\Domain\Model\Device;
use App\Domotic\Infrastructure\ApiPlatform\Resource\DeviceResource;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: DeviceResource::class, to: Device::class)]
readonly class DeviceResourceToDeviceMapper implements MapperInterface
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
        assert($dto instanceof DeviceResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, Device::class)) :
            new Device()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, Device::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof DeviceResource);
        assert($entity instanceof Device);

        $entity->setName($dto->name);
        $entity->setTechnicalName($dto->technicalName);

        return $entity;
    }
}
