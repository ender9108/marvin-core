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

namespace Marvin\Security\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Security\Domain\Model\LoginAttempt;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\LoginAttemptRepositoryInterface;
use Override;

/**
 * @extends ServiceEntityRepository<LoginAttempt>
 */
final class LoginAttemptOrmRepository extends ServiceEntityRepository implements LoginAttemptRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginAttempt::class);
    }

    #[Override]
    public function save(LoginAttempt $loginAttempt, bool $flush = true): void
    {
        $this->getEntityManager()->persist($loginAttempt);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(LoginAttempt $loginAttempt, bool $flush = true): void
    {
        $this->getEntityManager()->remove($loginAttempt);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function countBy(User $user): int
    {
        return $this->count([
            'user' => $user,
        ]);
    }

    #[Override]
    public function deleteBy(User $user): void
    {
        $this->createQueryBuilder('la')
            ->delete(LoginAttempt::class, 'la')
            ->where('la.user = :user')
            ->setParameter('user', $user->id->toBinary())
            ->getQuery()
            ->execute()
        ;
    }
}
