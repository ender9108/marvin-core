<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Domotic\Domain\Exception\CapabilityActionNotFound;
use Marvin\Domotic\Domain\Model\CapabilityAction;
use Marvin\Domotic\Domain\Repository\CapabilityActionRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\CapabilityActionId;
use Override;

/**
 * @extends ServiceEntityRepository<CapabilityAction>
 */
final class CapabilityActionOrmRepository extends ServiceEntityRepository implements CapabilityActionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CapabilityAction::class);
    }

    #[Override]
    public function save(CapabilityAction $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(CapabilityAction $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(CapabilityActionId $id): CapabilityAction
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw CapabilityActionNotFound::withId($id);
        }
        return $entity;
    }
}
