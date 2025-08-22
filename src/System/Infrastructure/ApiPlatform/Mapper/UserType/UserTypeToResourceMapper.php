<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\UserType;

use App\System\Domain\Model\UserType;
use App\System\Infrastructure\ApiPlatform\Resource\UserTypeResource;
use EnderLab\DddCqrsApiPlatformBundle\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: UserType::class, to: UserTypeResource::class)]
class UserTypeToResourceMapper extends AbstractMapper implements MapperInterface
{
    public function __construct(
        TranslatorInterface $translator,
        CacheInterface $cache,
    ) {
        parent::__construct($translator, $cache);
    }

    public function load(object $from, string $toClass, array $context): ApiResourceInterface
    {
        $entity = $from;
        assert($entity instanceof UserType);

        $dto = new UserTypeResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function populate(object $from, object $to, array $context): ApiResourceInterface
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof UserType);
        assert($dto instanceof UserTypeResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $this->translateDto($dto);
    }
}
