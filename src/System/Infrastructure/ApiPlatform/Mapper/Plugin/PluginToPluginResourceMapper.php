<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\Plugin;

use App\System\Domain\Model\Plugin;
use App\System\Infrastructure\ApiPlatform\Resource\PluginResource;
use App\System\Infrastructure\ApiPlatform\Resource\PluginStatusResource;
use Psr\Cache\InvalidArgumentException;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsMapper(from: Plugin::class, to: PluginResource::class)]
class PluginToPluginResourceMapper extends AbstractMapper implements MapperInterface
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
        assert($entity instanceof Plugin);
        $dto = new PluginResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Plugin);
        assert($dto instanceof PluginResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->description = $entity->getDescription();
        $dto->version = $entity->getVersion();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();
        $dto->status = $this->microMapper->map($entity->getStatus(), PluginStatusResource::class, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]);

        return $this->translateDto($dto);
    }
}
