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
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsMapper(from: CapabilityResource::class, to: Capability::class)]
class CapabilityResourceToCapabilityMapper extends AbstractMapper implements MapperInterface
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

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof CapabilityResource);
        assert($entity instanceof Capability);

        /* @todo Add your mapping here */

        return $entity;
    }
}
