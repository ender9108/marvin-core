<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\System\Domain\Exception\PluginStatusNotFound;
use Marvin\System\Domain\Model\PluginStatus;
use Marvin\System\Domain\Repository\PluginStatusRepositoryInterface;
use Marvin\System\Domain\ValueObject\Identity\PluginStatusId;
use Override;

/**
 * @extends ServiceEntityRepository<PluginStatus>
 */
final class PluginStatusOrmRepository extends ServiceEntityRepository implements PluginStatusRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PluginStatus::class);
    }

    #[Override]
    public function save(PluginStatus $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(PluginStatus $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(PluginStatusId $id): PluginStatus
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw PluginStatusNotFound::withId($id);
        }
        return $entity;
    }
}
