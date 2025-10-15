<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Domotic\Domain\Exception\DeviceActionNotFound;
use Marvin\Domotic\Domain\Model\DeviceAction;
use Marvin\Domotic\Domain\Repository\DeviceActionRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\DeviceActionId;
use Override;

/**
 * @extends ServiceEntityRepository<DeviceAction>
 */
final class DeviceActionOrmRepository extends ServiceEntityRepository implements DeviceActionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceAction::class);
    }

    #[Override]
    public function save(DeviceAction $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(DeviceAction $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(DeviceActionId $id): DeviceAction
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw DeviceActionNotFound::withId($id);
        }
        return $entity;
    }
}
