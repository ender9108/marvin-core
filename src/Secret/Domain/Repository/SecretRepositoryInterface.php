<?php

namespace Marvin\Secret\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\ValueObject\Identity\SecretId;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;

interface SecretRepositoryInterface
{
    public function save(Secret $secret, bool $flush = true): void;

    public function remove(Secret $secret, bool $flush = true): void;

    public function byId(SecretId $id): ?Secret;

    public function byKey(SecretKey $key): ?Secret;

    public function all(): array;

    public function byScope(SecretScope $scope): array;

    public function byCategory(SecretCategory $category): array;

    public function getNeedingRotation(): array;

    public function getExpired(): array;

    public function exists(SecretKey $key): bool;

    public function collection(
        /** @var array<string, mixed> $criterias */
        array $criterias = [],
        /** @var array<string, mixed> $orderBy */
        array $orderBy = [],
        int $page = 0,
        int $itemsPerPage = 20
    ): PaginatorInterface;
}
