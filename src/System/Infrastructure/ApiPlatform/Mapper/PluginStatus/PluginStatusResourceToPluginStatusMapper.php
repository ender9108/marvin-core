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
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsMapper(from: PluginStatusResource::class, to: PluginStatus::class)]
class PluginStatusResourceToPluginStatusMapper extends AbstractMapper implements MapperInterface
{
    public function __construct(
        private readonly QueryBus $queryBus,
        TranslatorInterface $translator,
        CacheInterface $cache,
    ) {
        parent::__construct($translator, $cache);
    }

    /**
     * @throws MissingModelException
     * @throws ExceptionInterface
     */
    public function load(object $from, string $toClass, array $context): object
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

    public function populate(object $from, object $to, array $context): object
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
