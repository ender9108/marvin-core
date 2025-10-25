<?php

namespace Marvin\Secret\Domain\Repository;

use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\ValueObject\Identity\SecretId;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;

interface SecretRepositoryInterface
{
    public function save(Secret $secret): void;

    public function remove(Secret $secret): void;

    public function byId(SecretId $id): ?Secret;

    public function byKey(SecretKey $key): ?Secret;

    public function all(): array;

    public function byScope(SecretScope $scope): array;

    public function byCategory(SecretCategory $category): array;

    public function getNeedingRotation(): array;

    public function getExpired(): array;

    public function exists(SecretKey $key): bool;
}
