<?php

namespace App\System\Infrastructure\Doctrine\Repository;

use App\System\Domain\Model\PluginStatus;
use App\System\Domain\Repository\PluginStatusRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrinePluginStatusRepository extends ServiceEntityRepository implements PluginStatusRepositoryInterface
{
    private const string ENTITY_CLASS = PluginStatus::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS);
    }

    public function add(PluginStatus $pluginStatus): void
    {
        $this->getEntityManager()->persist($pluginStatus);
    }

    public function remove(PluginStatus $pluginStatus): void
    {
        $this->getEntityManager()->remove($pluginStatus);
    }

    public function byId(int $id): ?PluginStatus
    {
        return $this->find($id);
    }

    public function byReference(string $reference): ?PluginStatus
    {
        return $this
            ->createQueryBuilder('ps')
            ->andWhere('ps.reference = :reference')
            ->setParameter('reference', $reference)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
