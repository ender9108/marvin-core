<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Domotic\Domain\Exception\DeviceNotFound;
use Marvin\Domotic\Domain\Model\Device;
use Marvin\Domotic\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Domotic\Domain\ValueObject\Identity\DeviceId;
use Override;

/**
 * @extends ServiceEntityRepository<Device>
 */
final class DeviceOrmRepository extends ServiceEntityRepository implements DeviceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }

    #[Override]
    public function save(Device $model, bool $flush = true): void
    {
        $this->getEntityManager()->persist($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function remove(Device $model, bool $flush = true): void
    {
        $this->getEntityManager()->remove($model);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    #[Override]
    public function byId(DeviceId $id): Device
    {
        $entity = $this->findOneBy(['id' => $id]);
        if (null === $entity) {
            throw DeviceNotFound::withId($id);
        }
        return $entity;
    }
}
