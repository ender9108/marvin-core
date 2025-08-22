<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Protocol;

use App\Domotic\Domain\Model\Protocol;
use App\Domotic\Infrastructure\ApiPlatform\Resource\ProtocolResource;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Protocol::class, to: ProtocolResource::class)]
readonly class ProtocolToProtocolResourceMapper extends AbstractMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        TranslatorInterface $translator,
        CacheInterface $cache,
    ) {
        parent::__construct($translator, $cache);
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Protocol);
        $dto = new ProtocolResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Protocol);
        assert($dto instanceof ProtocolResource);

        /* Add your mapping here */
        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $this->translateDto($dto);
    }
}
