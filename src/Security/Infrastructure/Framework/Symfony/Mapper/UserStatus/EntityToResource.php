<?php
namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper\UserStatus;

use Marvin\Security\Domain\Model\UserStatus;
use Marvin\Security\Presentation\Api\Resource\UserStatus\UserStatusResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: UserStatus::class, to: UserStatusResource::class)]
class EntityToResource implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): UserStatusResource
    {
        assert($from instanceof UserStatus);

        $dto = new UserStatusResource(
            $from->id,
            $from->label->value,
            $from->reference->value,
            $from->createdAt->value
        );

        return $dto;
    }

    public function populate(object $from, object $to, array $context): UserStatusResource
    {
        assert($from instanceof UserStatus);
        assert($to instanceof UserStatusResource);

        return $to;
    }
}
