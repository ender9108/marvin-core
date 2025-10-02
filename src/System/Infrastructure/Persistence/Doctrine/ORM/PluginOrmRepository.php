<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\Exception\PluginNotFound;
use Marvin\System\Domain\List\PluginStatusReference;
use Marvin\System\Domain\Model\Plugin;
use Marvin\System\Domain\Repository\PluginRepositoryInterface;
use Marvin\System\Domain\ValueObject\Identity\PluginId;
use Override;

/**
 * @extends ServiceEntityRepository<Plugin>
 */
final class PluginOrmRepository extends ServiceEntityRepository implements PluginRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plugin::class);
    }

    #[Override]
    public function save(Plugin $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(Plugin $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(PluginId $id): Plugin
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw PluginNotFound::withId($id);
        }
        return $entity;
    }

    #[Override]
    public function exists(Reference $reference): bool
    {
        $count = $this
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->innerJoin('p.status', 's')
            ->andWhere('p.reference = :reference')
            ->setParameter('reference', $reference->value)
            ->andWhere('s.reference = :status')
            ->setParameter('status', PluginStatusReference::STATUS_ENABLED->value)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count === 1;
    }

    #[Override]
    public function getByReference(Reference $reference): ?Plugin
    {
        return $this
            ->createQueryBuilder('p')
            ->andWhere('p.reference = :reference')
            ->setParameter('reference', $reference->value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
