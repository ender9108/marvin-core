<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\ProtocolStatus;

use App\Domotic\Domain\Model\ProtocolStatus;
use App\Domotic\Infrastructure\ApiPlatform\Resource\ProtocolStatusResource;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: ProtocolStatus::class, to: ProtocolStatusResource::class)]
class ProtocolStatusToProtocolStatusResourceMapper extends AbstractMapper implements MapperInterface
{
    public function __construct(
        private readonly MicroMapperInterface $microMapper,
        TranslatorInterface $translator,
        CacheInterface $cache,
    ) {
        parent::__construct($translator, $cache);
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof ProtocolStatus);
        $dto = new ProtocolStatusResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof ProtocolStatus);
        assert($dto instanceof ProtocolStatusResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $this->translateDto($dto);
    }
}
