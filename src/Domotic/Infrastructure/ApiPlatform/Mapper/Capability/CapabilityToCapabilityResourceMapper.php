<?php

namespace App\Domotic\Infrastructure\ApiPlatform\Mapper\Capability;

use App\Domotic\Domain\Model\Capability;
use App\Domotic\Infrastructure\ApiPlatform\Resource\CapabilityResource;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;

#[AsMapper(from: Capability::class, to: CapabilityResource::class)]
class CapabilityToCapabilityResourceMapper extends AbstractMapper implements MapperInterface
{
    public function __construct(
        TranslatorInterface $translator,
        CacheInterface $cache,
    ) {
        parent::__construct($translator, $cache);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Capability);
        $dto = new CapabilityResource();
        $dto->id = $entity->getId();

        return $this->translateDto($dto);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof Capability);
        assert($dto instanceof CapabilityResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $this->translateDto($dto);
    }
}
