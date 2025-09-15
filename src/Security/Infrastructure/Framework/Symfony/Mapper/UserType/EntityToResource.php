<?php
namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper\UserType;

use Marvin\Security\Domain\Model\UserType;
use Marvin\Security\Presentation\Api\Resource\UserType\UserTypeResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: UserType::class, to: UserTypeResource::class)]
class EntityToResource implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): UserTypeResource
    {
        assert($from instanceof UserType);

        $dto = new UserTypeResource(
            $from->id,
            $from->label->value,
            $from->reference->value,
            $from->createdAt->value
        );

        return $dto;
    }

    public function populate(object $from, object $to, array $context): UserTypeResource
    {
        assert($from instanceof UserType);
        assert($to instanceof UserTypeResource);

        return $to;
    }
}
