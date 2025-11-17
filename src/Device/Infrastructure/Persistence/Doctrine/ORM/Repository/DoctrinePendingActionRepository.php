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

namespace Marvin\Device\Infrastructure\Persistence\Doctrine\ORM\Repository;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Device\Domain\Model\PendingAction;
use Marvin\Device\Domain\Repository\PendingActionRepositoryInterface;
use Marvin\Device\Domain\ValueObject\PendingActionStatus;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\PendingActionId;

/**
 * Doctrine ORM implementation of PendingActionRepositoryInterface
 */
final class DoctrinePendingActionRepository extends ServiceEntityRepository implements PendingActionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PendingAction::class);
    }

    public function byId(PendingActionId $id): ?PendingAction
    {
        return $this->find($id->toString());
    }

    public function byCorrelationId(CorrelationId $correlationId): ?PendingAction
    {
        return $this->findOneBy(['correlationId' => $correlationId->toString()]);
    }

    public function findActivePendingActionForDevice(DeviceId $deviceId): ?PendingAction
    {
        return $this->createQueryBuilder('pa')
            ->where('pa.deviceId = :deviceId')
            ->andWhere('pa.status = :status')
            ->setParameter('deviceId', $deviceId->toString())
            ->setParameter('status', PendingActionStatus::WAITING->value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function byStatus(PendingActionStatus $status): array
    {
        return $this->findBy(['status' => $status->value]);
    }

    public function findExpired(): array
    {
        $now = new DateTimeImmutable();

        return $this->createQueryBuilder('pa')
            ->where('pa.status = :status')
            ->andWhere('DATE_ADD(pa.createdAt, pa.timeoutSeconds, \'SECOND\') <= :now')
            ->setParameter('status', PendingActionStatus::WAITING->value)
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult();
    }

    public function save(PendingAction $pendingAction): void
    {
        $this->getEntityManager()->persist($pendingAction);
        $this->getEntityManager()->flush();
    }

    public function remove(PendingAction $pendingAction): void
    {
        $this->getEntityManager()->remove($pendingAction);
        $this->getEntityManager()->flush();
    }

    public function hasActivePendingAction(DeviceId $deviceId): bool
    {
        $count = $this->createQueryBuilder('pa')
            ->select('COUNT(pa.id)')
            ->where('pa.deviceId = :deviceId')
            ->andWhere('pa.status = :status')
            ->setParameter('deviceId', $deviceId->toString())
            ->setParameter('status', PendingActionStatus::WAITING->value)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
