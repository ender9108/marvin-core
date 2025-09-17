<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\Mapper\User;

use Marvin\Security\Domain\Model\User;
use Marvin\Security\Presentation\Api\Resource\User\ChangeUserEmailResource;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: User::class, to: ChangeUserEmailResource::class)]
final readonly class EntityToChangeUserEmailResource implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): ChangeUserEmailResource
    {
        assert($from instanceof User);

        $dto = new ChangeUserEmailResource($from->email->value);

        $dto->id = $from->id->toString();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): ChangeUserEmailResource
    {
        assert($from instanceof User);
        assert($to instanceof ChangeUserEmailResource);

        return $to;
    }
}
