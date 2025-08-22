<?php

namespace App\System\Infrastructure\ApiPlatform\Mapper\UserStatus;

use App\System\Domain\Model\UserStatus;
use App\System\Infrastructure\ApiPlatform\Resource\UserStatusResource;
use EnderLab\DddCqrsApiPlatformBundle\ApiResourceInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\AbstractMapper;

#[AsMapper(from: UserStatus::class, to: UserStatusResource::class)]
class UserStatusToResourceMapper extends AbstractMapper implements MapperInterface
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
        assert($entity instanceof UserStatus);

        $dto = new UserStatusResource();
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

        assert($entity instanceof UserStatus);
        assert($dto instanceof UserStatusResource);

        $dto->label = $entity->getLabel();
        $dto->reference = $entity->getReference();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->updatedAt = $entity->getUpdatedAt();
        $dto->createdBy = $entity->getCreatedBy();
        $dto->updatedBy = $entity->getUpdatedBy();

        return $this->translateDto($dto);
    }
}
