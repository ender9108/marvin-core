<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\PluginStatus;

use App\System\Domain\Model\PluginStatus;
use App\System\Infrastructure\ApiPlatform\Resource\PluginStatusResource;
use Psr\Cache\InvalidArgumentException;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsMapper(from: PluginStatus::class, to: PluginStatusResource::class)]
class PluginStatusToPluginStatusResourceMapper extends AbstractMapper implements MapperInterface
{
    public function __construct(
        TranslatorInterface $translator,
        CacheInterface $cache,
    ) {
        parent::__construct($translator, $cache);
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof PluginStatus);
        $dto = new PluginStatusResource();
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

        assert($entity instanceof PluginStatus);
        assert($dto instanceof PluginStatusResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $this->translateDto($dto);
    }
}
