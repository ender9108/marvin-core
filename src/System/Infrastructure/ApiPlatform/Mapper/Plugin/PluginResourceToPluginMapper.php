<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\Plugin;

use App\System\Domain\Model\Plugin;
use App\System\Domain\Model\PluginStatus;
use App\System\Infrastructure\ApiPlatform\Resource\PluginResource;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: PluginResource::class, to: Plugin::class)]
readonly class PluginResourceToPluginMapper  implements MapperInterface
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
    public function load(object $from, string $toClass, array $context): Plugin
    {
        $dto = $from;
        assert($dto instanceof PluginResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, Plugin::class)) :
            new Plugin()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, Plugin::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): Plugin
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof PluginResource);
        assert($entity instanceof Plugin);

        $entity
            ->setLabel($dto->label)
            ->setReference($dto->reference)
            ->setDescription($dto->description)
            ->setVersion($dto->version)
            ->setStatus($this->microMapper->map(
                $dto->status,
                PluginStatus::class,
                [MicroMapperInterface::MAX_DEPTH => 0]
            ))
        ;

        return $entity;
    }
}
