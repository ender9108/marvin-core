<?php

namespace App\System\Infrastructure\Doctrine\Repository;

use App\System\Domain\Model\Plugin;
use App\System\Domain\Model\PluginStatus;
use App\System\Domain\Repository\PluginRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrinePluginRepository extends ServiceEntityRepository implements PluginRepositoryInterface
{
    private const string ENTITY_CLASS = Plugin::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(Plugin $plugin): void
    {
        $this->getEntityManager()->persist($plugin);
    }

    public function remove(Plugin $plugin): void
    {
        $this->getEntityManager()->remove($plugin);
    }

    public function byId(int $id): ?Plugin
    {
        return $this->find($id);
    }

    public function isEnabled(string $reference): bool
    {
        $count = $this
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

        return $count > 0;
    }

    public function getByReference(string $reference): ?Plugin
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
