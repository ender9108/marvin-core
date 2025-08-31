<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Zone;

use App\Domotic\Domain\Model\Zone;
use App\Domotic\Infrastructure\ApiPlatform\Resource\ZoneResource;
use Psr\Cache\InvalidArgumentException;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsMapper(from: Zone::class, to: ZoneResource::class)]
class ZoneToZoneResourceMapper extends AbstractMapper implements MapperInterface
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
        assert($entity instanceof Zone);
        $dto = new ZoneResource();
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

        assert($entity instanceof Zone);
        assert($dto instanceof ZoneResource);

        $dto->label = $entity->getLabel();
        $dto->area = $entity->getArea();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $this->translateDto($dto);
    }
}
