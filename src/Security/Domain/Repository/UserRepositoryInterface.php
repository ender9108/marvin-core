<?php
namespace Marvin\Security\Domain\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Email;

interface UserRepositoryInterface
{
    public function save(User $user, bool $flush = true): void;

    public function remove(User $user, bool $flush = true): void;

    public function countSameEnabledUserType(User $user): int;

    public function byId(UserId $userId): User;

    public function byEmail(Email $email): ?User;

    public function byIdentifier(string $identifier): ?User;

    public function getUserCollection(
        array $criterias = [],
        array $orderBy = [],
        int $page = 0,
        int $itemsPerPage = 20
    ): PaginatorInterface;
}
