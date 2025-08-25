<?php

namespace App\Domotic\Infrastructure\Doctrine\Repository;

use App\Domotic\Domain\Model\Protocol;
use App\Domotic\Domain\Repository\ProtocolRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineProtocolRepository extends ServiceEntityRepository implements ProtocolRepositoryInterface
{
    private const string ENTITY_CLASS = Protocol::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(Protocol $protocol): void
    {
        $this->getEntityManager()->persist($protocol);
    }

    public function remove(Protocol $protocol): void
    {
        $this->getEntityManager()->remove($protocol);
    }

    public function byId(string $id): ?Protocol
    {
        return $this->find($id);
    }

    public function isEnabled(string $reference): bool
    {
        /*$count = $this
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->innerJoin('p.status', 's')
            ->andWhere('p.reference = :reference')
            ->setParameter('reference', $reference)
            ->andWhere('s.reference = :status')
            ->setParameter('status', PluginStatus::STATUS_ENABLED)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;*/
        return true;
    }

    public function getByReference(string $reference): ?Protocol
    {
        return $this
            ->createQueryBuilder('p')
            ->andWhere('p.reference = :reference')
            ->setParameter('reference', $reference)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
