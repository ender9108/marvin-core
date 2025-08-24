<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Group;

use App\Domotic\Domain\Model\Group;
use App\Domotic\Infrastructure\ApiPlatform\Resource\GroupResource;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: Group::class, to: GroupResource::class)]
class GroupToGroupResourceMapper extends AbstractMapper implements MapperInterface
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
        assert($entity instanceof Group);
        $dto = new GroupResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Group);
        assert($dto instanceof GroupResource);

        $dto->name = $entity->getName();
        $dto->slug = $entity->getSlug();
        $dto->devices = $entity->getDevices();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $this->translateDto($dto);
    }
}
