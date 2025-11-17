<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Security\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Security\Domain\Model\User;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;

interface UserRepositoryInterface
{
    public function save(User $user, bool $flush = true): void;

    public function remove(User $user, bool $flush = true): void;

    public function countSameEnabledUserType(User $user): int;

    public function byId(UserId $userId): User;

    public function byEmail(Email $email): ?User;

    public function emailExists(Email|string $email): bool;

    public function byIdentifier(string $identifier): ?User;

    public function collection(
        /** @var array<string, mixed> $criterias */
        array $criterias = [],
        /** @var array<string, mixed> $orderBy */
        array $orderBy = [],
        int $page = 0,
        int $itemsPerPage = 20
    ): PaginatorInterface;
}
