<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\PluginStatus;

use App\System\Domain\Model\PluginStatus;
use App\System\Infrastructure\ApiPlatform\Resource\PluginStatusResource;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;
use EnderLab\DddCqrsBundle\Application\Query\FindItemQuery;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: PluginStatusResource::class, to: PluginStatus::class)]
readonly class PluginStatusResourceToPluginStatusMapper implements MapperInterface
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    /**
     * @throws MissingModelException
     * @throws ExceptionInterface
     */
    public function load(object $from, string $toClass, array $context): PluginStatus
    {
        $dto = $from;
        assert($dto instanceof PluginStatusResource);

        $entity = $dto->id ?
            $this->queryBus->ask(new FindItemQuery($dto->id, PluginStatus::class)) :
            new PluginStatus()
        ;

        if (!$entity) {
            throw new MissingModelException($dto->id, PluginStatus::class);
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): PluginStatus
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof PluginStatusResource);
        assert($entity instanceof PluginStatus);

        $entity
            ->setLabel($dto->label)
            ->setReference($dto->reference)
        ;

        return $entity;
    }
}
