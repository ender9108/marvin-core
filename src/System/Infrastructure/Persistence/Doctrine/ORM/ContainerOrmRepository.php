<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\System\Domain\Exception\ContainerNotFound;
use Marvin\System\Domain\Model\Container;
use Marvin\System\Domain\Repository\ContainerRepositoryInterface;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Override;

/**
 * @extends ServiceEntityRepository<Container>
 */
final class ContainerOrmRepository extends ServiceEntityRepository implements ContainerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Container::class);
    }

    #[Override]
    public function save(Container $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(Container $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(ContainerId $id): Container
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw ContainerNotFound::withId($id);
        }
        return $entity;
    }
}
